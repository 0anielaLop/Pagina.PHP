<?php
// panel.php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require_once 'config.php';

// Procesar acciones (UPDATE y DELETE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'update') {
                $updateSql = "UPDATE animelist_db_list SET status = ?, progress = ?, p_score = ?, fecha_agregado = NOW() WHERE id_list = ? AND email = ?";
                $database->execute($updateSql, [
                    $_POST['status'],
                    $_POST['progress'],
                    $_POST['p_score'],
                    $_POST['list_id'],
                    $_SESSION['email']
                ]);
                $_SESSION['success_message'] = "Anime updated successfully!";
                
            } elseif ($_POST['action'] === 'delete') {
                $deleteSql = "DELETE FROM animelist_db_list WHERE id_list = ? AND email = ?";
                $database->execute($deleteSql, [$_POST['list_id'], $_SESSION['email']]);
                $_SESSION['success_message'] = "Anime removed from your list!";
            }
            
            header('Location: panel.php');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            header('Location: panel.php');
            exit;
        }
    }
}

$pageTitle = "My List";
include __DIR__ . '/include/header.php';

// Mostrar mensajes
if (isset($_SESSION['success_message'])) {
    echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}

try {
    // Obtener lista de animes del usuario
    $sql = "
        SELECT al.*, a.name, a.img, a.n_episodes, a.synopsis, g.name as genre_name,
               COALESCE(AVG(all_users.p_score), 0) as global_score,
               COUNT(all_users.id_list) as total_ratings
        FROM animelist_db_list al 
        JOIN animelist_db_animes a ON al.id_anime = a.id_anime 
        JOIN animelist_db_genres g ON a.id_genres = g.id_genres
        LEFT JOIN animelist_db_list all_users ON a.id_anime = all_users.id_anime
        WHERE al.email = ?
        GROUP BY al.id_list, a.id_anime, g.name
        ORDER BY 
            CASE al.status 
                WHEN 'Watching' THEN 1
                WHEN 'Completed' THEN 2
                WHEN 'OnHold' THEN 3
                WHEN 'Dropped' THEN 4
                WHEN 'PlanToWatch' THEN 5
            END,
            al.fecha_agregado DESC
    ";
    $stmt = $database->query($sql, [$_SESSION['email']]);
    $userAnimeList = $stmt->fetchAll();
    
    // Organizar por status
    $animeByStatus = [
        'Watching' => [],
        'Completed' => [],
        'OnHold' => [],
        'Dropped' => [],
        'PlanToWatch' => []
    ];
    
    foreach ($userAnimeList as $anime) {
        $animeByStatus[$anime['status']][] = $anime;
    }
    
} catch (PDOException $e) {
    $animeByStatus = [
        'Watching' => [],
        'Completed' => [],
        'OnHold' => [],
        'Dropped' => [],
        'PlanToWatch' => []
    ];
    $error = "Error loading list: " . $e->getMessage();
}

// Estadísticas
$totalAnime = count($userAnimeList);
$stats = [
    'Watching' => count($animeByStatus['Watching']),
    'Completed' => count($animeByStatus['Completed']),
    'OnHold' => count($animeByStatus['OnHold']),
    'Dropped' => count($animeByStatus['Dropped']),
    'PlanToWatch' => count($animeByStatus['PlanToWatch'])
];

// Código para la barra de porcentajes
$percentages = [];
foreach ($stats as $status => $count) {
    $percentages[$status] = $totalAnime > 0 ? ($count / $totalAnime) * 100 : 0;
}

// Colores para cada status
$statusColors = [
    'Watching' => 'stat-watching',
    'Completed' => 'stat-completed',
    'OnHold' => 'stat-onhold',
    'Dropped' => 'stat-dropped',
    'PlanToWatch' => 'stat-plantowatch'
];

$statusColorClasses = [
    'Watching' => 'stat-watching-color',
    'Completed' => 'stat-completed-color',
    'OnHold' => 'stat-onhold-color',
    'Dropped' => 'stat-dropped-color',
    'PlanToWatch' => 'stat-plantowatch-color'
];
?>

<section class="panel-container">
    <div class="panel-header">
        <h1 class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        
        <!-- Barra de estadísticas -->
        <?php if ($totalAnime > 0): ?>
        <div class="mal-stats-container">
            <div class="mal-stats-bar">
                <?php foreach ($stats as $status => $count): 
                    if ($count > 0 && $percentages[$status] > 0):
                    $percentage = number_format($percentages[$status], 1);
                ?>
                <div class="mal-stat-segment <?php echo $statusColors[$status]; ?>" 
                     style="width: <?php echo $percentage; ?>%"
                     data-tooltip="<?php echo $status; ?>: <?php echo $count; ?> (<?php echo $percentage; ?>%)"
                     onclick="showStatus('<?php echo strtolower($status); ?>')">
                    <?php if ($percentage >= 8): ?>
                        <span class="mal-stat-percentage"><?php echo $percentage; ?>%</span>
                    <?php endif; ?>
                </div>
                <?php endif;
                endforeach; ?>
            </div>
            
            <div class="mal-stats-info">
                <div class="mal-total-anime">
                    <strong>Total Anime:</strong> <?php echo $totalAnime; ?>
                </div>
                <div class="mal-stats-numbers">
                    <?php foreach ($stats as $status => $count): ?>
                    <div class="mal-stat-item" onclick="showStatus('<?php echo strtolower($status); ?>')">
                        <div class="mal-stat-color <?php echo $statusColorClasses[$status]; ?>"></div>
                        <span class="mal-stat-name"><?php echo $status; ?>:</span>
                        <span class="mal-stat-count"><?php echo $count; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="no-stats-message">
            <p>No anime in your list yet. <a href="index.php">Start adding some!</a></p>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="panel-content">
        <h2>My Anime List</h2>
        
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($totalAnime === 0): ?>
            <div class="empty-list">
                <p>Your list is empty. Add some anime from the catalog!</p>
                <a href="index.php" class="btn btn-primary">Browse Catalog</a>
            </div>
        <?php else: ?>
            <!-- Navegación por Tabs -->
            <div class="status-tabs-nav">
                <button class="status-tab active" onclick="showStatus('all')">All (<?php echo $totalAnime; ?>)</button>
                <?php foreach ($stats as $status => $count): 
                    if ($count > 0): ?>
                    <button class="status-tab" onclick="showStatus('<?php echo strtolower($status); ?>')">
                        <?php echo $status; ?> (<?php echo $count; ?>)
                    </button>
                    <?php endif;
                endforeach; ?>
            </div>
            
            <!-- Tabla All -->
            <div class="status-section active" id="status-all">
                <h3 class="status-title">All Anime (<?php echo $totalAnime; ?>)</h3>
                <?php 
                // Guardar la lista original
                $originalList = $userAnimeList;
                include './anime_table.php'; 
                ?>
            </div>
            
            <!-- Tablas por Status - ESTA ES LA PARTE QUE ESTABA COMENTADA -->
            <?php foreach ($animeByStatus as $status => $animes): 
                if (!empty($animes)): ?>
                <div class="status-section" id="status-<?php echo strtolower($status); ?>">
                    <h3 class="status-title"><?php echo $status; ?> (<?php echo count($animes); ?>)</h3>
                    <?php 
                    // Pasar solo los animes de este status a la tabla
                    $userAnimeList = $animes;
                    include './anime_table.php';
                    ?>
                </div>
                <?php endif;
            endforeach; ?>
            
            <?php 
            // Restaurar la lista original
            $userAnimeList = $originalList;
            ?>
        <?php endif; ?>
    </div>
    
    <div class="panel-actions">
        <a href="index.php" class="btn btn-primary">Add More Anime</a>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
</section>

<!-- Modal para editar anime -->
<div id="editModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2 id="edit-modal-title">Edit Anime</h2>
            <span class="close-btn" onclick="closeEditForm()">&times;</span>
        </div>
        <form method="POST" action="panel.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_list_id" name="list_id">
            <input type="hidden" id="edit_anime_id" name="anime_id">
            
            <div class="form-group">
                <label>Status:</label>
                <select name="status" id="edit_status" class="status-select" required>
                    <option value="Watching">Watching</option>
                    <option value="Completed">Completed</option>
                    <option value="OnHold">On Hold</option>
                    <option value="Dropped">Dropped</option>
                    <option value="PlanToWatch">Plan to Watch</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Progress:</label>
                <div class="progress-container">
                    <input type="number" id="edit_progress" name="progress" class="form-input progress-input" 
                           min="0" value="0" required onchange="updateEditStatus()">
                    <span class="progress-max" id="edit-progress-max">/ 0</span>
                </div>
            </div>
            
            <div class="form-group">
                <label>Your Score:</label>
                <select name="p_score" id="edit_p_score" class="score-select" required>
                    <option value="">Select Score</option>
                    <option value="10">(10) Masterpiece</option>
                    <option value="9">(9) Great</option>
                    <option value="8">(8) Very Good</option>
                    <option value="7">(7) Good</option>
                    <option value="6">(6) Fine</option>
                    <option value="5">(5) Average</option>
                    <option value="4">(4) Bad</option>
                    <option value="3">(3) Very Bad</option>
                    <option value="2">(2) Horrible</option>
                    <option value="1">(1) Appalling</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeEditForm()">Cancel</button>
                <button type="submit" class="btn-save">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" method="POST" action="panel.php" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="delete_list_id" name="list_id">
</form>

<script>
// Navegación entre tabs
function showStatus(status) {
    document.querySelectorAll('.status-section').forEach(section => {
        section.classList.remove('active');
    });
    
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.getElementById('status-' + status).classList.add('active');
    document.querySelector(`.status-tab[onclick="showStatus('${status}')"]`).classList.add('active');
}

// Funciones para el modal de edición
function editAnime(listId, animeId, animeName, maxEpisodes, currentStatus, currentProgress, currentScore) {
    document.getElementById('edit_list_id').value = listId;
    document.getElementById('edit_anime_id').value = animeId;
    document.getElementById('edit-modal-title').textContent = 'Edit: ' + animeName;
    document.getElementById('edit_progress').max = maxEpisodes;
    document.getElementById('edit-progress-max').textContent = '/ ' + maxEpisodes;
    
    document.getElementById('edit_status').value = currentStatus;
    document.getElementById('edit_progress').value = currentProgress;
    document.getElementById('edit_p_score').value = currentScore;
    
    document.getElementById('editModal').style.display = 'flex';
}

function updateEditStatus() {
    const progress = parseInt(document.getElementById('edit_progress').value);
    const maxEpisodes = parseInt(document.getElementById('edit_progress').max);
    const statusSelect = document.getElementById('edit_status');
    
    if (progress >= maxEpisodes && maxEpisodes > 0) {
        statusSelect.value = 'Completed';
    } else if (progress > 0) {
        statusSelect.value = 'Watching';
    }
}

function closeEditForm() {
    document.getElementById('editModal').style.display = 'none';
}

function removeAnime(listId, animeName) {
    if (confirm('Are you sure you want to remove "' + animeName + '" from your list?')) {
        document.getElementById('delete_list_id').value = listId;
        document.getElementById('deleteForm').submit();
    }
}

// Cerrar modales
document.getElementById('editModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeEditForm();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEditForm();
    }
});

document.getElementById('edit_progress').addEventListener('input', updateEditStatus);
</script>

<?php
include __DIR__ . '/include/footer.php';
?>
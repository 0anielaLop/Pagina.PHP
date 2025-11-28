<?php
session_start();
require_once 'config.php';

$pageTitle = "AnimeList";
include __DIR__ . '/include/header.php';

// Obtener todos los animes disponibles
try {
    $sql = "
        SELECT a.*, g.name as genre_name,
               COALESCE(AVG(al.p_score), 0) as global_score,
               COUNT(al.id_list) as total_ratings
        FROM animelist_db_animes a 
        JOIN animelist_db_genres g ON a.id_genres = g.id_genres 
        LEFT JOIN animelist_db_list al ON a.id_anime = al.id_anime
        WHERE a.state = 1 
        GROUP BY a.id_anime
        ORDER BY a.name
    ";
    $stmt = $database->query($sql);
    $animes = $stmt->fetchAll();
} catch (PDOException $e) {
    $animes = [];
    $error = "Error loading catalog: " . $e->getMessage();
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Anime Catalog</h1>
        <p class="hero-description">Discover and add your favorite animes to your personal list</p>
        
        <?php if (!isset($_SESSION['username'])): ?>
            <div class="hero-buttons">
                <a href="registro.php" class="btn btn-primary">Sign Up</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </div>
        <?php else: ?>
            <div class="welcome-user">
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | 
                   <a href="panel.php" class="btn btn-primary">My List</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Anime Catalog -->
<section class="catalog-container">
    <h2 class="section-title">All Animes</h2>
    
    <?php if (isset($error)): ?>
        <p style="color: red; text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <div class="anime-cards-grid">
        <?php foreach ($animes as $anime): 
            $globalScore = number_format($anime['global_score'], 1);
            $totalRatings = $anime['total_ratings'];
        ?>
            <div class="anime-card" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.7)), url('assets/img/<?php echo htmlspecialchars($anime['img']); ?>');">
                <div class="anime-card-content">
                    <div class="anime-info">
                        <h3 class="anime-title"><?php echo htmlspecialchars($anime['name']); ?></h3>
                        <p class="anime-genre"><?php echo htmlspecialchars($anime['genre_name']); ?></p>
                        <p class="anime-episodes"><?php echo $anime['n_episodes']; ?> episodes</p>
                        
                        <!-- Mostrar puntuación global -->
                        <div class="global-score">
                            <span class="score-value">★ <?php echo $globalScore; ?></span>
                            <span class="score-ratings">(<?php echo $totalRatings; ?> ratings)</span>
                        </div>
                        
                        <p class="anime-synopsis"><?php echo substr($anime['synopsis'], 0, 120); ?>...</p>
                    </div>
                    
                    <?php if (isset($_SESSION['username'])): ?>
                        <button class="btn-add-to-list" 
                                onclick="showAddForm(<?php echo $anime['id_anime']; ?>, '<?php echo htmlspecialchars($anime['name']); ?>', <?php echo $anime['n_episodes']; ?>)">
                            Add to List
                        </button>
                    <?php else: ?>
                        <a href="login.php" class="btn-login-to-add">Login to Add</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Modal para agregar a lista -->
<div id="addModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2 id="modal-title">Add to List</h2>
            <span class="close-btn" onclick="closeAddForm()">&times;</span>
        </div>
        <form id="addToListForm" method="POST" action="list.php">
            <input type="hidden" id="anime_id" name="anime_id">
            
            <div class="form-group">
                <label>Status:</label>
                <select name="status" id="status" class="status-select" required>
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
                    <input type="number" id="progress" name="progress" class="form-input progress-input" 
                           min="0" value="0" required onchange="updateStatus()">
                    <span class="progress-max" id="progress-max">/ 0</span>
                </div>
            </div>
            
            <div class="form-group">
                <label>Your Score:</label>
                <select name="p_score" class="score-select" required>
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
                <button type="button" class="btn-cancel" onclick="closeAddForm()">Cancel</button>
                <button type="submit" class="btn-save">Save to List</button>
            </div>
        </form>
    </div>
</div>

<script>
// Funciones para el modal
function showAddForm(animeId, animeName, maxEpisodes) {
    document.getElementById('anime_id').value = animeId;
    document.getElementById('modal-title').textContent = 'Add: ' + animeName;
    document.getElementById('progress').max = maxEpisodes;
    document.getElementById('progress-max').textContent = '/ ' + maxEpisodes;
    document.getElementById('addModal').style.display = 'flex';
    
    // Reset form
    document.getElementById('progress').value = 0;
    document.getElementById('status').value = 'Watching';
    document.querySelectorAll('.score-select').forEach(select => {
        select.value = '';
    });
}

function updateStatus() {
    const progress = parseInt(document.getElementById('progress').value);
    const maxEpisodes = parseInt(document.getElementById('progress').max);
    const statusSelect = document.getElementById('status');
    
    // Auto-update status based on progress
    if (progress >= maxEpisodes && maxEpisodes > 0) {
        statusSelect.value = 'Completed';
    } else if (progress > 0) {
        statusSelect.value = 'Watching';
    }
}

function closeAddForm() {
    document.getElementById('addModal').style.display = 'none';
}

// Cerrar modal al hacer click fuera
document.getElementById('addModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeAddForm();
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddForm();
    }
});

// Auto-update status when progress changes
document.getElementById('progress').addEventListener('input', updateStatus);
</script>

<?php include __DIR__ . '/include/footer.php'; ?>
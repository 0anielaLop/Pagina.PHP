<?php
// anime_table.php
?>
<div class="mal-table-container">
    <table class="mal-table">
        <thead>
            <tr>
                <th class="col-number">#</th>
                <th class="col-image">Image</th>
                <th class="col-title">Anime Title</th>
                <th class="col-score">Score</th>
                <th class="col-progress">Progress</th>
                <th class="col-genre">Genre</th>
                <th class="col-global">Global Score</th>
                <th class="col-date">Date Added</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($userAnimeList as $index => $anime): 
                $globalScore = number_format($anime['global_score'], 1);
                $progressPercent = $anime['n_episodes'] > 0 ? min(100, ($anime['progress'] / $anime['n_episodes']) * 100) : 0;
                $dateAdded = date('M j, Y', strtotime($anime['fecha_agregado']));
            ?>
            <tr class="anime-row">
                <td class="col-number"><?php echo $index + 1; ?></td>
                <td class="col-image">
                    <img src="assets/img/<?php echo htmlspecialchars($anime['img']); ?>" 
                         alt="<?php echo htmlspecialchars($anime['name']); ?>"
                         class="anime-thumbnail"
                         onerror="this.src='https://via.placeholder.com/60x80/333/666?text=Anime'">
                </td>
                <td class="col-title">
                    <div class="anime-title"><?php echo htmlspecialchars($anime['name']); ?></div>
                </td>
                <td class="col-score">
                    <div class="score-display">
                        <span class="score-value"><?php echo $anime['p_score']; ?></span>
                        <span class="score-max">/10</span>
                    </div>
                </td>
                <td class="col-progress">
                    <div class="progress-text"><?php echo $anime['progress']; ?>/<?php echo $anime['n_episodes']; ?></div>
                    <?php if ($anime['n_episodes'] > 0): ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progressPercent; ?>%"></div>
                    </div>
                    <?php endif; ?>
                </td>
                <td class="col-genre">
                    <span class="genre-tag"><?php echo htmlspecialchars($anime['genre_name']); ?></span>
                </td>
                <td class="col-global">
                    <div class="global-score-display">
                        <span class="global-score"><?php echo $globalScore; ?></span>
                        <span class="global-ratings">(<?php echo $anime['total_ratings']; ?>)</span>
                    </div>
                </td>
                <td class="col-date">
                    <span class="date-added"><?php echo $dateAdded; ?></span>
                </td>
                <td class="col-actions">
                    <button class="btn-edit" onclick="editAnime(<?php echo $anime['id_list']; ?>, <?php echo $anime['id_anime']; ?>, '<?php echo htmlspecialchars($anime['name']); ?>', <?php echo $anime['n_episodes']; ?>, '<?php echo $anime['status']; ?>', <?php echo $anime['progress']; ?>, <?php echo $anime['p_score']; ?>)">
                        Edit
                    </button>
                    <button class="btn-remove" onclick="removeAnime(<?php echo $anime['id_list']; ?>, '<?php echo htmlspecialchars($anime['name']); ?>')">
                        Remove
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
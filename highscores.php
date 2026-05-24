<?php
$page_title = "High Scores";
include "header.php";

// Get filter parameters
$difficulty_filter = isset($_GET["difficulty"]) ? $_GET["difficulty"] : "all";
$limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 10;

// Base query
$query = "SELECT username, score, total_questions, percentage, difficulty, played_at 
          FROM scores 
          WHERE 1=1";

if($difficulty_filter != "all") {
    $query .= " AND difficulty = '$difficulty_filter'";
}

$query .= " ORDER BY percentage DESC, score DESC, played_at ASC LIMIT $limit";

$result = $conn->query($query);

// Get user's personal best
$personal_best = null;
if($logged_in) {
    $pb_query = "SELECT MAX(percentage) as best_percentage, MAX(score) as best_score 
                 FROM scores WHERE user_id = {$_SESSION['user_id']}";
    $pb_result = $conn->query($pb_query);
    $personal_best = $pb_result->fetch_assoc();
}

// Get statistics
$stats_query = "SELECT 
                COUNT(*) as total_attempts,
                AVG(percentage) as avg_percentage,
                MAX(percentage) as highest_percentage,
                COUNT(DISTINCT user_id) as unique_players
                FROM scores";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Check if stats are null (no data yet)
$has_data = ($stats['total_attempts'] > 0);
?>

<style>
.highscores-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 40px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 24px;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.highscores-title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 800;
    background: linear-gradient(135deg, #ffa500, #ff6b4a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.stat-card {
    text-align: center;
    padding: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card .stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #00f5ff;
}

.stat-card .stat-label {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 5px;
}

.filter-bar {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.filter-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 40px;
    padding: 8px 20px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
    color: #9ca3af;
    text-decoration: none;
}

.filter-btn:hover {
    background: rgba(0, 245, 255, 0.2);
    color: #00f5ff;
}

.filter-btn.active {
    background: linear-gradient(135deg, #00f5ff, #7c4dff);
    color: #fff;
}

.filter-btn.difficulty-easy.active {
    background: linear-gradient(135deg, #05ffa1, #00c48a);
}
.filter-btn.difficulty-medium.active {
    background: linear-gradient(135deg, #ffa500, #ff8c00);
}
.filter-btn.difficulty-hard.active {
    background: linear-gradient(135deg, #ff3366, #cc0044);
}

.highscores-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}

.highscores-table th,
.highscores-table td {
    padding: 12px 10px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.highscores-table th {
    color: #00f5ff;
    font-weight: 600;
    font-size: 14px;
}

.highscores-table td {
    color: #e5e7eb;
    font-size: 14px;
}

.highscores-table tr:hover td {
    background: rgba(255, 255, 255, 0.03);
}

.rank-1 td:first-child {
    color: #ffd700;
    font-weight: bold;
    font-size: 18px;
}
.rank-2 td:first-child {
    color: #c0c0c0;
    font-weight: bold;
    font-size: 18px;
}
.rank-3 td:first-child {
    color: #cd7f32;
    font-weight: bold;
    font-size: 18px;
}

.percentage-bar-container {
    width: 80px;
    display: inline-block;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    overflow: hidden;
    height: 6px;
}

.percentage-bar {
    height: 6px;
    border-radius: 10px;
    transition: width 0.5s;
}

.personal-best {
    margin-top: 25px;
    padding: 15px;
    background: rgba(0, 245, 255, 0.1);
    border-radius: 16px;
    text-align: center;
    border: 1px solid rgba(0, 245, 255, 0.3);
}

.personal-best p {
    margin: 0;
    color: #00f5ff;
}

.no-data {
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}

body.light-mode .highscores-container {
    background: rgba(255, 255, 255, 0.9);
}

body.light-mode .highscores-table th {
    color: #7c4dff;
}

body.light-mode .highscores-table td {
    color: #1a1a2e;
}

body.light-mode .highscores-table tr:hover td {
    background: rgba(0, 0, 0, 0.05);
}

body.light-mode .stat-card {
    background: rgba(0, 0, 0, 0.05);
}

body.light-mode .filter-btn {
    background: rgba(0, 0, 0, 0.05);
    color: #4a5568;
}

@media (max-width: 768px) {
    .highscores-container {
        padding: 20px;
        margin: 20px;
    }
    .highscores-table th,
    .highscores-table td {
        padding: 8px 5px;
        font-size: 11px;
    }
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="highscores-container">
    <h1 class="highscores-title">🏆 <?php echo t("high_scores"); ?></h1>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $has_data ? $stats['total_attempts'] : '0'; ?></div>
            <div class="stat-label"><?php echo t("total_attempts"); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $has_data ? round($stats['avg_percentage']) . '%' : '0%'; ?></div>
            <div class="stat-label"><?php echo t("average_score"); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $has_data ? $stats['highest_percentage'] . '%' : '0%'; ?></div>
            <div class="stat-label"><?php echo t("highest_score"); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $has_data ? $stats['unique_players'] : '0'; ?></div>
            <div class="stat-label"><?php echo t("players"); ?></div>
        </div>
    </div>
    
    <!-- Filter Bar -->
    <div class="filter-bar">
        <a href="?difficulty=all&limit=<?php echo $limit; ?>&lang=<?php echo $lang; ?>" 
           class="filter-btn <?php echo ($difficulty_filter == 'all') ? 'active' : ''; ?>">
            📋 <?php echo t("all"); ?>
        </a>
        <a href="?difficulty=easy&limit=<?php echo $limit; ?>&lang=<?php echo $lang; ?>" 
           class="filter-btn difficulty-easy <?php echo ($difficulty_filter == 'easy') ? 'active' : ''; ?>">
            🟢 <?php echo t("easy"); ?>
        </a>
        <a href="?difficulty=medium&limit=<?php echo $limit; ?>&lang=<?php echo $lang; ?>" 
           class="filter-btn difficulty-medium <?php echo ($difficulty_filter == 'medium') ? 'active' : ''; ?>">
            🟡 <?php echo t("medium"); ?>
        </a>
        <a href="?difficulty=hard&limit=<?php echo $limit; ?>&lang=<?php echo $lang; ?>" 
           class="filter-btn difficulty-hard <?php echo ($difficulty_filter == 'hard') ? 'active' : ''; ?>">
            🔴 <?php echo t("hard"); ?>
        </a>
    </div>
    
    <!-- High Scores Table -->
    <?php if($result->num_rows > 0): ?>
    <table class="highscores-table">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo t("player"); ?></th>
                <th><?php echo t("score"); ?></th>
                <th><?php echo t("percentage"); ?></th>
                <th><?php echo t("difficulty"); ?></th>
                <th><?php echo t("date"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $rank = 1;
            while($row = $result->fetch_assoc()): 
                $percentage_color = $row['percentage'] >= 70 ? '#05ffa1' : ($row['percentage'] >= 50 ? '#ffa500' : '#ff3366');
                $rank_class = ($rank <= 3) ? "rank-$rank" : "";
            ?>
            <tr class="<?php echo $rank_class; ?>">
                <td><?php echo $rank; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: <?php echo $percentage_color; ?>; font-weight: bold;"><?php echo $row['percentage']; ?>%</span>
                        <div class="percentage-bar-container">
                            <div class="percentage-bar" style="width: <?php echo $row['percentage']; ?>%; background: <?php echo $percentage_color; ?>;"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <?php
                    $difficulty_icon = '';
                    if($row['difficulty'] == 'easy') $difficulty_icon = '🟢';
                    elseif($row['difficulty'] == 'medium') $difficulty_icon = '🟡';
                    elseif($row['difficulty'] == 'hard') $difficulty_icon = '🔴';
                    else $difficulty_icon = '📋';
                    echo $difficulty_icon . ' ' . t($row['difficulty']);
                    ?>
                </td>
                <td><?php echo date('d.m.Y H:i', strtotime($row['played_at'])); ?></td>
            </tr>
            <?php 
            $rank++;
            endwhile; 
            ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">
        🎮 <?php echo t("no_scores_yet"); ?><br>
        <a href="quiz_setup.php?lang=<?php echo $lang; ?>" class="button start" style="display: inline-block; margin-top: 20px;"><?php echo t("be_first"); ?></a>
    </div>
    <?php endif; ?>
    
    <!-- Personal Best -->
    <?php if($logged_in && $personal_best && $personal_best['best_percentage'] > 0): ?>
    <div class="personal-best">
        <p>🎯 <?php echo t("your_personal_best"); ?>: 
           <strong><?php echo $personal_best['best_score']; ?></strong> / <?php echo t("max_score"); ?> 
           (<strong><?php echo $personal_best['best_percentage']; ?>%</strong>)
        </p>
    </div>
    <?php endif; ?>
    
    <a href="index.php?lang=<?php echo $lang; ?>" class="button admin" style="display: block; text-align: center; margin-top: 20px;">⬅ <?php echo t("back_home"); ?></a>
</div>

<?php include "footer.php"; ?>
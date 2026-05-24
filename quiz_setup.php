<?php
$page_title = "Quiz Setup";
include "header.php";

// Բազայից վերցնել հարցերի ընդհանուր քանակը
$total_easy = $conn->query("SELECT COUNT(*) as count FROM questions WHERE difficulty = 'easy'")->fetch_assoc()["count"];
$total_medium = $conn->query("SELECT COUNT(*) as count FROM questions WHERE difficulty = 'medium'")->fetch_assoc()["count"];
$total_hard = $conn->query("SELECT COUNT(*) as count FROM questions WHERE difficulty = 'hard'")->fetch_assoc()["count"];
$total_all = $total_easy + $total_medium + $total_hard;

// POST-ից ստանալ ընտրությունները
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["start_quiz"])) {
    $difficulty = $_POST["difficulty"];
    $question_count = (int)$_POST["question_count"];
    
    // Պահպանել session-ում
    $_SESSION["quiz_difficulty"] = $difficulty;
    $_SESSION["quiz_question_count"] = $question_count;
    
    // Ուղղորդել խաղի էջ
    header("Location: game.php?lang=$lang");
    exit;
}

// Default արժեքներ
$default_difficulty = isset($_SESSION["quiz_difficulty"]) ? $_SESSION["quiz_difficulty"] : "all";
$default_count = isset($_SESSION["quiz_question_count"]) ? $_SESSION["quiz_question_count"] : 10;
?>

<style>
.setup-container {
    max-width: 600px;
    margin: 50px auto;
    padding: 40px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 24px;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.setup-title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 800;
    background: linear-gradient(135deg, #00f5ff, #7c4dff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.setup-group {
    margin-bottom: 30px;
    text-align: center;
}

.setup-label {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    display: block;
    color: #00f5ff;
}

.difficulty-buttons, .count-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}

.difficulty-btn, .count-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 50px;
    padding: 12px 28px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    color: #9ca3af;
    text-decoration: none;
    display: inline-block;
}

.difficulty-btn:hover, .count-btn:hover {
    transform: translateY(-2px);
}

.difficulty-btn.active, .count-btn.active {
    background: linear-gradient(135deg, #00f5ff, #7c4dff);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 0 15px rgba(0, 245, 255, 0.3);
}

/* Difficulty specific styles */
.difficulty-easy {
    border-color: #05ffa1;
    color: #05ffa1;
}
.difficulty-easy.active {
    background: linear-gradient(135deg, #05ffa1, #00c48a);
    color: #fff;
}

.difficulty-medium {
    border-color: #ffa500;
    color: #ffa500;
}
.difficulty-medium.active {
    background: linear-gradient(135deg, #ffa500, #ff8c00);
    color: #fff;
}

.difficulty-hard {
    border-color: #ff3366;
    color: #ff3366;
}
.difficulty-hard.active {
    background: linear-gradient(135deg, #ff3366, #cc0044);
    color: #fff;
}

.info-text {
    text-align: center;
    margin-top: 15px;
    font-size: 13px;
    color: #9ca3af;
}

.info-text span {
    color: #00f5ff;
    font-weight: bold;
}

.start-btn {
    background: linear-gradient(135deg, #7c4dff, #00f5ff);
    width: 100%;
    padding: 16px;
    font-size: 18px;
    margin-top: 20px;
}

.start-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 30px rgba(124, 77, 255, 0.4);
}

body.light-mode .setup-container {
    background: rgba(255, 255, 255, 0.9);
}

body.light-mode .difficulty-btn,
body.light-mode .count-btn {
    background: rgba(0, 0, 0, 0.05);
    color: #4a5568;
}

body.light-mode .difficulty-easy {
    color: #00a86b;
}
body.light-mode .difficulty-medium {
    color: #cc7a00;
}
body.light-mode .difficulty-hard {
    color: #cc0044;
}
</style>

<div class="setup-container">
    <h1 class="setup-title">🎮 <?php echo t("quiz_setup"); ?></h1>
    
    <form method="POST">
        <!-- Difficulty Selection -->
        <div class="setup-group">
            <div class="setup-label">🎯 <?php echo t("select_difficulty"); ?></div>
            <div class="difficulty-buttons">
                <button type="button" class="difficulty-btn difficulty-all <?php echo ($default_difficulty == 'all') ? 'active' : ''; ?>" data-difficulty="all">
                    📋 <?php echo t("all_difficulties"); ?> (<?php echo $total_all; ?>)
                </button>
                <button type="button" class="difficulty-btn difficulty-easy <?php echo ($default_difficulty == 'easy') ? 'active' : ''; ?>" data-difficulty="easy">
                    🟢 <?php echo t("easy"); ?> (<?php echo $total_easy; ?>)
                </button>
                <button type="button" class="difficulty-btn difficulty-medium <?php echo ($default_difficulty == 'medium') ? 'active' : ''; ?>" data-difficulty="medium">
                    🟡 <?php echo t("medium"); ?> (<?php echo $total_medium; ?>)
                </button>
                <button type="button" class="difficulty-btn difficulty-hard <?php echo ($default_difficulty == 'hard') ? 'active' : ''; ?>" data-difficulty="hard">
                    🔴 <?php echo t("hard"); ?> (<?php echo $total_hard; ?>)
                </button>
            </div>
            <input type="hidden" name="difficulty" id="selected_difficulty" value="<?php echo $default_difficulty; ?>">
        </div>
        
        <!-- Question Count Selection -->
        <div class="setup-group">
            <div class="setup-label">📊 <?php echo t("select_question_count"); ?></div>
            <div class="count-buttons">
                <?php
                $count_options = [5, 10, 15, 20, 30, 50];
                foreach($count_options as $option):
                    $active_class = ($default_count == $option) ? 'active' : '';
                ?>
                <button type="button" class="count-btn <?php echo $active_class; ?>" data-count="<?php echo $option; ?>">
                    <?php echo $option; ?>
                </button>
                <?php endforeach; ?>
                <button type="button" class="count-btn <?php echo ($default_count == 'all') ? 'active' : ''; ?>" data-count="all">
                    📋 <?php echo t("all_questions"); ?>
                </button>
            </div>
            <input type="hidden" name="question_count" id="selected_count" value="<?php echo $default_count; ?>">
            <div class="info-text" id="countInfo">
                <?php echo t("available_questions"); ?>: <span id="availableCount"><?php echo $total_all; ?></span>
            </div>
        </div>
        
        <button type="submit" name="start_quiz" class="start-btn">🚀 <?php echo t("start_quiz"); ?></button>
    </form>
    
    <a href="index.php?lang=<?php echo $lang; ?>" class="button admin" style="display: block; text-align: center; margin-top: 20px;">⬅ <?php echo t("back_home"); ?></a>
</div>

<script>
// Difficulty selection
const difficultyBtns = document.querySelectorAll('.difficulty-btn');
const difficultyInput = document.getElementById('selected_difficulty');
const availableSpan = document.getElementById('availableCount');

// Question count selection
const countBtns = document.querySelectorAll('.count-btn');
const countInput = document.getElementById('selected_count');

// Available questions count per difficulty
const availableCounts = {
    all: <?php echo $total_all; ?>,
    easy: <?php echo $total_easy; ?>,
    medium: <?php echo $total_medium; ?>,
    hard: <?php echo $total_hard; ?>
};

// Difficulty button click
difficultyBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        difficultyBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const difficulty = this.dataset.difficulty;
        difficultyInput.value = difficulty;
        
        // Update available questions count
        availableSpan.textContent = availableCounts[difficulty];
        
        // Check if selected count is valid for this difficulty
        const selectedCount = countInput.value;
        const maxAvailable = availableCounts[difficulty];
        
        if(selectedCount !== 'all' && parseInt(selectedCount) > maxAvailable) {
            // Find and click the 'all' button
            const allBtn = Array.from(countBtns).find(btn => btn.dataset.count === 'all');
            if(allBtn) {
                allBtn.click();
            }
        }
    });
});

// Count button click
countBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        countBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const count = this.dataset.count;
        countInput.value = count;
    });
});
</script>

<?php include "footer.php"; ?>
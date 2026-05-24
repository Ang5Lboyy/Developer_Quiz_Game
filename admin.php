<?php
$page_title = "Admin Panel";
include "header.php";

// Ստուգել արդյոք օգտատերը մուտք է գործել և ադմին է
if(!$logged_in || !$is_admin) {
    header("Location: login.php?lang=$lang");
    exit;
}

$lang = isset($_SESSION["lang"]) ? $_SESSION["lang"] : "en";

// ========== ADD NEW QUESTION ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_question_btn"])) {
    $question = $_POST["question"];
    $a = $_POST["a"];
    $b = $_POST["b"];
    $c = $_POST["c"];
    $d = $_POST["d"];
    $answer = (int)$_POST["answer"];
    $difficulty = $_POST["difficulty"];
    $explanation = $_POST["explanation"];
    
    $stmt = $conn->prepare("INSERT INTO questions (question, option_a, option_b, option_c, option_d, answer, difficulty, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiss", $question, $a, $b, $c, $d, $answer, $difficulty, $explanation);
    
    if ($stmt->execute()) {
        $success_msg = ($lang == 'hy') ? "✓ Հարցը հաջողությամբ ավելացվեց" : "✓ Question Added Successfully";
        echo "<script>alert('$success_msg'); window.location.href='admin.php?lang=$lang';</script>";
        exit;
    } else {
        $error_msg = ($lang == 'hy') ? "✗ Սխալ հարցը ավելացնելիս" : "✗ Error adding question";
        echo "<script>alert('$error_msg');</script>";
    }
    $stmt->close();
}

// ========== EDIT QUESTION ==========
if(isset($_GET["edit"])) {
    $edit_id = (int)$_GET["edit"];
    $edit_query = $conn->query("SELECT * FROM questions WHERE id = $edit_id");
    $edit_data = $edit_query->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_question_btn"])) {
    $edit_id = (int)$_POST["edit_id"];
    $question = $_POST["question"];
    $a = $_POST["a"];
    $b = $_POST["b"];
    $c = $_POST["c"];
    $d = $_POST["d"];
    $answer = (int)$_POST["answer"];
    $difficulty = $_POST["difficulty"];
    $explanation = $_POST["explanation"];
    
    $stmt = $conn->prepare("UPDATE questions SET question=?, option_a=?, option_b=?, option_c=?, option_d=?, answer=?, difficulty=?, explanation=? WHERE id=?");
    $stmt->bind_param("sssssissi", $question, $a, $b, $c, $d, $answer, $difficulty, $explanation, $edit_id);
    
    if ($stmt->execute()) {
        $success_msg = ($lang == 'hy') ? "✓ Հարցը հաջողությամբ թարմացվեց" : "✓ Question Updated Successfully";
        echo "<script>alert('$success_msg'); window.location.href='admin.php?lang=$lang';</script>";
        exit;
    }
    $stmt->close();
}

// ========== DELETE QUESTION ==========
if(isset($_GET["delete"])) {
    $delete_id = (int)$_GET["delete"];
    $conn->query("DELETE FROM questions WHERE id = $delete_id");
    header("Location: admin.php?lang=$lang");
    exit;
}

// ========== RESHUFFLE ==========
if(isset($_GET["reshuffle_all"])) {
    unset($_SESSION["shuffled_questions"]);
    header("Location: admin.php?lang=$lang");
    exit;
}

// ========== GET ALL QUESTIONS ==========
$questions_result = $conn->query("SELECT * FROM questions ORDER BY id DESC");
?>

<style>
.admin-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 40px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 24px;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.admin-title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 30px;
    font-weight: 800;
    background: linear-gradient(135deg, #ff3366, #ff6b4a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #9ca3af;
    font-size: 13px;
}

input[type="text"], 
input[type="number"], 
select, 
textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.04);
    color: #fff;
    font-size: 14px;
    transition: all 0.3s;
}

textarea {
    resize: vertical;
    min-height: 80px;
}

input:focus, select:focus, textarea:focus {
    border-color: #ff3366;
    outline: none;
    background: rgba(255, 255, 255, 0.08);
}

select option {
    background: #1a1a2e;
    color: #fff;
}

.difficulty-select option[value="easy"] { color: #05ffa1; }
.difficulty-select option[value="medium"] { color: #ffa500; }
.difficulty-select option[value="hard"] { color: #ff3366; }

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

button {
    background: linear-gradient(135deg, #7c4dff, #00f5ff);
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 14px;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 30px rgba(124, 77, 255, 0.4);
}

hr {
    margin: 30px 0;
    border-color: rgba(255, 255, 255, 0.1);
}

.questions-list {
    max-height: 600px;
    overflow-y: auto;
}

.question-item {
    background: rgba(255, 255, 255, 0.03);
    padding: 15px;
    margin: 10px 0;
    border-radius: 12px;
    transition: all 0.3s;
}

.question-item:hover {
    background: rgba(255, 255, 255, 0.06);
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.difficulty-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.difficulty-easy { background: rgba(5, 255, 161, 0.2); color: #05ffa1; }
.difficulty-medium { background: rgba(255, 165, 0, 0.2); color: #ffa500; }
.difficulty-hard { background: rgba(255, 51, 102, 0.2); color: #ff3366; }

.question-actions {
    display: flex;
    gap: 10px;
}

.edit-btn, .delete-btn {
    padding: 5px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.3s;
}

.edit-btn {
    background: rgba(0, 245, 255, 0.2);
    color: #00f5ff;
}

.edit-btn:hover {
    background: rgba(0, 245, 255, 0.4);
}

.delete-btn {
    background: rgba(255, 51, 102, 0.2);
    color: #ff3366;
}

.delete-btn:hover {
    background: rgba(255, 51, 102, 0.4);
}

.question-text {
    margin: 10px 0;
    color: #fff;
}

.question-options {
    font-size: 12px;
    color: #9ca3af;
    margin: 8px 0;
}

.correct-answer {
    color: #05ffa1;
    font-size: 12px;
    margin: 5px 0;
}

.explanation-preview {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.edit-form {
    background: rgba(255, 255, 255, 0.05);
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 20px;
}

.edit-form h3 {
    color: #00f5ff;
    margin-bottom: 15px;
}

.cancel-btn {
    background: #374151;
    margin-top: 0;
    margin-bottom: 10px;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.reshuffle-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    color: #9ca3af;
    transition: all 0.3s;
}

.reshuffle-btn:hover {
    background: rgba(0, 245, 255, 0.2);
    color: #00f5ff;
}

body.light-mode .admin-container {
    background: rgba(255, 255, 255, 0.9);
}

body.light-mode input,
body.light-mode select,
body.light-mode textarea {
    background: rgba(0, 0, 0, 0.05);
    color: #1a1a2e;
    border-color: rgba(0, 0, 0, 0.1);
}

body.light-mode select option {
    background: #fff;
    color: #1a1a2e;
}

body.light-mode .question-item {
    background: rgba(0, 0, 0, 0.03);
}

body.light-mode .question-text {
    color: #1a1a2e;
}

@media (max-width: 768px) {
    .admin-container {
        padding: 20px;
        margin: 20px;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
    .question-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

<div class="admin-container">
    <h1 class="admin-title">➕ <?php echo t("Add New Question"); ?></h1>
    
    <?php if(isset($edit_data)): ?>
    <!-- Edit Form -->
    <div class="edit-form">
        <h3>✏️ <?php echo t("edit_question"); ?></h3>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?php echo $edit_data['id']; ?>">
            
            <div class="form-group">
                <label>❓ <?php echo t("question"); ?></label>
                <textarea name="question" required><?php echo htmlspecialchars($edit_data['question']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>A) <?php echo t("option_a"); ?></label>
                    <input type="text" name="a" value="<?php echo htmlspecialchars($edit_data['option_a']); ?>" required>
                </div>
                <div class="form-group">
                    <label>B) <?php echo t("option_b"); ?></label>
                    <input type="text" name="b" value="<?php echo htmlspecialchars($edit_data['option_b']); ?>" required>
                </div>
                <div class="form-group">
                    <label>C) <?php echo t("option_c"); ?></label>
                    <input type="text" name="c" value="<?php echo htmlspecialchars($edit_data['option_c']); ?>" required>
                </div>
                <div class="form-group">
                    <label>D) <?php echo t("option_d"); ?></label>
                    <input type="text" name="d" value="<?php echo htmlspecialchars($edit_data['option_d']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>✅ <?php echo t("correct_answer"); ?> (1-4)</label>
                    <input type="number" name="answer" min="1" max="4" value="<?php echo $edit_data['answer']; ?>" required>
                </div>
                <div class="form-group">
                    <label>🎯 <?php echo t("difficulty"); ?></label>
                    <select name="difficulty" class="difficulty-select" required>
                        <option value="easy" <?php echo ($edit_data['difficulty'] == 'easy') ? 'selected' : ''; ?>>🟢 Easy</option>
                        <option value="medium" <?php echo ($edit_data['difficulty'] == 'medium') ? 'selected' : ''; ?>>🟡 Medium</option>
                        <option value="hard" <?php echo ($edit_data['difficulty'] == 'hard') ? 'selected' : ''; ?>>🔴 Hard</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>💡 <?php echo t("explanation"); ?></label>
                <textarea name="explanation" placeholder="Explain why the correct answer is right..." required><?php echo htmlspecialchars($edit_data['explanation']); ?></textarea>
            </div>
            
            <div class="form-row">
                <button type="submit" name="edit_question_btn">💾 <?php echo t("save_changes"); ?></button>
                <a href="admin.php?lang=<?php echo $lang; ?>" class="cancel-btn" style="display: block; text-align: center; background: #374151; text-decoration: none; padding: 14px; border-radius: 14px; color: #fff;">❌ <?php echo t("cancel"); ?></a>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- Add New Question Form -->
    <form method="POST">
        <div class="form-group">
            <label>❓ <?php echo t("question"); ?></label>
            <textarea name="question" placeholder="Enter your question here..." required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>A) <?php echo t("option_a"); ?></label>
                <input type="text" name="a" placeholder="Option A" required>
            </div>
            <div class="form-group">
                <label>B) <?php echo t("option_b"); ?></label>
                <input type="text" name="b" placeholder="Option B" required>
            </div>
            <div class="form-group">
                <label>C) <?php echo t("option_c"); ?></label>
                <input type="text" name="c" placeholder="Option C" required>
            </div>
            <div class="form-group">
                <label>D) <?php echo t("option_d"); ?></label>
                <input type="text" name="d" placeholder="Option D" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>✅ <?php echo t("correct_answer"); ?> (1-4)</label>
                <input type="number" name="answer" min="1" max="4" placeholder="1, 2, 3, or 4" required>
            </div>
            <div class="form-group">
                <label>🎯 <?php echo t("difficulty"); ?></label>
                <select name="difficulty" class="difficulty-select" required>
                    <option value="easy">🟢 Easy</option>
                    <option value="medium" selected>🟡 Medium</option>
                    <option value="hard">🔴 Hard</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>💡 <?php echo t("explanation"); ?></label>
            <textarea name="explanation" placeholder="Explain why the correct answer is right..." required></textarea>
        </div>
        
        <button type="submit" name="add_question_btn">➕ <?php echo t("add_question"); ?></button>
    </form>
    
    <hr>
    
    <div class="header-actions">
        <h2 class="admin-title" style="font-size: 24px; margin: 0;">📋 <?php echo t("existing_questions"); ?></h2>
        <a href="?reshuffle_all=1&lang=<?php echo $lang; ?>" class="reshuffle-btn">🔄 <?php echo t("reshuffle"); ?></a>
    </div>
    
    <div class="questions-list">
        <?php if($questions_result->num_rows > 0): ?>
            <?php while($q = $questions_result->fetch_assoc()): ?>
                <div class="question-item">
                    <div class="question-header">
                        <div>
                            <span class="difficulty-badge difficulty-<?php echo $q['difficulty']; ?>">
                                <?php 
                                if($q['difficulty'] == 'easy') echo '🟢 Easy';
                                elseif($q['difficulty'] == 'medium') echo '🟡 Medium';
                                else echo '🔴 Hard';
                                ?>
                            </span>
                            <span style="margin-left: 10px; color: #9ca3af;">ID: <?php echo $q['id']; ?></span>
                        </div>
                        <div class="question-actions">
                            <a href="?edit=<?php echo $q['id']; ?>&lang=<?php echo $lang; ?>" class="edit-btn">✏️ <?php echo t("edit"); ?></a>
                            <a href="?delete=<?php echo $q['id']; ?>&lang=<?php echo $lang; ?>" class="delete-btn" onclick="return confirm('<?php echo t("delete_confirm"); ?>')">🗑️ <?php echo t("delete"); ?></a>
                        </div>
                    </div>
                    <div class="question-text"><strong><?php echo htmlspecialchars($q['question']); ?></strong></div>
                    <div class="question-options">
                        A: <?php echo htmlspecialchars($q['option_a']); ?> | 
                        B: <?php echo htmlspecialchars($q['option_b']); ?> | 
                        C: <?php echo htmlspecialchars($q['option_c']); ?> | 
                        D: <?php echo htmlspecialchars($q['option_d']); ?>
                    </div>
                    <div class="correct-answer">✅ <?php echo t("correct_answer"); ?>: <?php echo $q['answer']; ?></div>
                    <?php if($q['explanation']): ?>
                    <div class="explanation-preview">💡 <?php echo t("explanation_label"); ?>: <?php echo htmlspecialchars(substr($q['explanation'], 0, 100)) . (strlen($q['explanation']) > 100 ? '...' : ''); ?></div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                📭 <?php echo t("no_questions_yet"); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <a href="index.php?lang=<?php echo $lang; ?>" class="button admin" style="display: block; text-align: center; margin-top: 20px;">⬅ <?php echo t("back_home"); ?></a>
</div>

<?php include "footer.php"; ?>
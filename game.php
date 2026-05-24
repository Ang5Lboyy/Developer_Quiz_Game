<?php
$page_title = "Developer Quiz - Game";
include "header.php";

// ========== GET SETTINGS FROM SESSION ==========
$selected_difficulty = isset($_SESSION["quiz_difficulty"]) ? $_SESSION["quiz_difficulty"] : "all";
$selected_count = isset($_SESSION["quiz_question_count"]) ? $_SESSION["quiz_question_count"] : 10;

// ========== TIMER SETTINGS ==========
$total_quiz_time = 300; // 5 րոպե

// ========== GET LIFELINES STATUS ==========
$fifty_fifty_available = 1;
$audience_available = 1;
if($logged_in) {
    $lifeline_query = $conn->query("SELECT fifty_fifty_available, audience_available FROM users WHERE id = {$_SESSION['user_id']}");
    if($lifeline_data = $lifeline_query->fetch_assoc()) {
        $fifty_fifty_available = $lifeline_data['fifty_fifty_available'];
        $audience_available = $lifeline_data['audience_available'];
    }
}

// ===================================

// Բազայից վերցնել հարցերը
if($selected_difficulty == 'all') {
    $result = $conn->query("SELECT * FROM questions");
} else {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE difficulty = ?");
    $stmt->bind_param("s", $selected_difficulty);
    $stmt->execute();
    $result = $stmt->get_result();
}

$all_questions = [];
while($row = $result->fetch_assoc()){
    $all_questions[] = $row;
}
if(isset($stmt)) $stmt->close();

$total_available = count($all_questions);

if($selected_count == 'all') {
    $selected_count = $total_available;
} else {
    $selected_count = (int)$selected_count;
    if($selected_count > $total_available) $selected_count = $total_available;
}
if($selected_count < 1) $selected_count = $total_available;

$questions = $all_questions;
shuffle($questions);
$questions = array_slice($questions, 0, $selected_count);

$total_questions = count($questions);
$score = 0;
$wrong_answers = [];

$difficulty_icons = ['easy' => '🟢', 'medium' => '🟡', 'hard' => '🔴'];

// Ստուգել պատասխանները
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_quiz"])){
    foreach($questions as $q){
        $id = $q["id"];
        $user_answer = isset($_POST["q$id"]) ? (int)$_POST["q$id"] : 0;
        $is_correct = ($user_answer == $q["answer"]);
        if($is_correct){
            $score++;
        } else {
            $wrong_answers[] = [
                'question' => $q["question"],
                'user_answer' => $user_answer,
                'correct_answer' => $q["answer"],
                'explanation' => $q["explanation"],
                'options' => [
                    1 => $q["option_a"],
                    2 => $q["option_b"],
                    3 => $q["option_c"],
                    4 => $q["option_d"]
                ]
            ];
        }
    }
    
    $percentage = ($total_questions > 0) ? round(($score / $total_questions) * 100) : 0;
    
    if($logged_in) {
        $difficulty_to_save = ($selected_difficulty == 'all') ? 'all' : $selected_difficulty;
        $save_stmt = $conn->prepare("INSERT INTO scores (user_id, username, score, total_questions, percentage, difficulty) VALUES (?, ?, ?, ?, ?, ?)");
        $save_stmt->bind_param("isiiis", $_SESSION["user_id"], $_SESSION["username"], $score, $total_questions, $percentage, $difficulty_to_save);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    if($percentage >= 90) {
        $grade = t("excellent");
        $grade_color = "#05ffa1";
        $grade_icon = "🌟";
    } elseif($percentage >= 70) {
        $grade = t("very_good");
        $grade_color = "#00f5ff";
        $grade_icon = "👍";
    } elseif($percentage >= 50) {
        $grade = t("good");
        $grade_color = "#ffa500";
        $grade_icon = "📚";
    } elseif($percentage >= 30) {
        $grade = t("average");
        $grade_color = "#ff6b4a";
        $grade_icon = "⚠️";
    } else {
        $grade = t("poor");
        $grade_color = "#ff3366";
        $grade_icon = "💪";
    }
    
    ?>
    <div class="result-box">
        <h1 class="result-title">🏆 <?php echo t("quiz_finished"); ?></h1>
        <div class="score-circle"><?php echo $score; ?> / <?php echo $total_questions; ?></div>
        
        <div class="percentage-container">
            <div class="percentage-label">📊 <?php echo t("your_score_percentage"); ?></div>
            <div class="percentage-value" style="color: <?php echo $grade_color; ?>;"><?php echo $percentage; ?>%</div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?php echo $percentage; ?>%; background: <?php echo $grade_color; ?>;"></div>
            </div>
            <div class="grade-box" style="border-color: <?php echo $grade_color; ?>;">
                <span class="grade-icon"><?php echo $grade_icon; ?></span>
                <span class="grade-text" style="color: <?php echo $grade_color; ?>;"><?php echo $grade; ?></span>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo $score; ?></div>
                <div class="stat-label">✅ <?php echo t("correct_answers"); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($wrong_answers); ?></div>
                <div class="stat-label">❌ <?php echo t("wrong_answers"); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_questions; ?></div>
                <div class="stat-label">📋 <?php echo t("total_questions"); ?></div>
            </div>
        </div>
        
        <?php if(count($wrong_answers) > 0): ?>
        <div class="explanations-section">
            <h2 class="explanations-title">📚 <?php echo t("explanations"); ?></h2>
            <?php foreach($wrong_answers as $wrong): ?>
            <div class="explanation-card">
                <div class="explanation-question">❓ <?php echo htmlspecialchars(t($wrong['question'])); ?></div>
                <div class="explanation-wrong">❌ <?php echo t("your_answer"); ?>: <strong><?php echo htmlspecialchars(t($wrong['options'][$wrong['user_answer']])); ?></strong></div>
                <div class="explanation-correct">✅ <?php echo t("correct_answer"); ?>: <strong><?php echo htmlspecialchars(t($wrong['options'][$wrong['correct_answer']])); ?></strong></div>
                <div class="explanation-text">💡 <strong><?php echo t("explanation_label"); ?>:</strong> <?php echo htmlspecialchars(t($wrong['explanation'])); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="result-buttons">
            <a href="index.php?lang=<?php echo $lang; ?>" class="button start">🏠 <?php echo t("back_home"); ?></a>
            <a href="highscores.php?lang=<?php echo $lang; ?>" class="button" style="background: linear-gradient(135deg, #ffa500, #ff6b4a);">🏆 <?php echo t("high_scores"); ?></a>
            <a href="quiz_setup.php?lang=<?php echo $lang; ?>" class="button admin">🎮 <?php echo t("new_quiz"); ?></a>
        </div>
    </div>
    
    <style>
    .explanations-section { margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); text-align: left; }
    .explanations-title { font-size: 20px; margin-bottom: 15px; color: #00f5ff; text-align: center; }
    .explanation-card { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 15px; margin-bottom: 15px; border-left: 4px solid #ff3366; }
    .explanation-question { font-weight: 600; margin-bottom: 10px; color: #fff; }
    .explanation-wrong { color: #ff3366; margin-bottom: 5px; font-size: 14px; }
    .explanation-correct { color: #05ffa1; margin-bottom: 5px; font-size: 14px; }
    .explanation-text { color: #9ca3af; margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 13px; line-height: 1.5; }
    .result-buttons { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 20px; }
    .result-buttons .button { margin: 0; width: auto; min-width: 120px; }
    body.light-mode .explanation-card { background: rgba(0,0,0,0.05); }
    body.light-mode .explanation-question { color: #1a1a2e; }
    @media (max-width: 768px) { .result-buttons .button { width: 100%; } }
    </style>
    <?php
    
    include "footer.php";
    exit;
}
?>

<style>
.timer-container { position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
.timer-box { background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px); border-radius: 50px; padding: 12px 24px; text-align: center; border: 2px solid #00f5ff; box-shadow: 0 0 20px rgba(0, 245, 255, 0.3); animation: pulse 2s infinite; }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(0, 245, 255, 0.4); } 70% { box-shadow: 0 0 0 15px rgba(0, 245, 255, 0); } 100% { box-shadow: 0 0 0 0 rgba(0, 245, 255, 0); } }
.timer-label { font-size: 12px; color: #9ca3af; letter-spacing: 1px; }
.timer-value { font-size: 28px; font-weight: bold; color: #00f5ff; font-family: monospace; }
.timer-value.warning { color: #ff3366; animation: blink 1s infinite; }
@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
#soundToggleBtn { background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px); border: 2px solid #7c4dff; border-radius: 50px; padding: 12px 20px; cursor: pointer; font-size: 20px; transition: all 0.3s; width: auto; display: inline-block; }
#soundToggleBtn:hover { transform: scale(1.05); box-shadow: 0 0 20px rgba(124, 77, 255, 0.4); }

/* Lifelines Styles */
.lifelines-container { position: fixed; top: 20px; left: 20px; z-index: 1000; display: flex; flex-direction: column; gap: 10px; }
.lifeline-btn { background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px); border: 2px solid #ffa500; border-radius: 50px; padding: 10px 18px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; color: #ffa500; display: flex; align-items: center; gap: 8px; }
.lifeline-btn:hover:not(.disabled) { transform: scale(1.05); box-shadow: 0 0 20px rgba(255, 165, 0, 0.4); background: rgba(255, 165, 0, 0.2); }
.lifeline-btn.disabled { opacity: 0.5; cursor: not-allowed; filter: grayscale(0.3); }
.lifeline-fifty { border-color: #7c4dff; color: #7c4dff; }
.lifeline-fifty:hover:not(.disabled) { box-shadow: 0 0 20px rgba(124, 77, 255, 0.4); background: rgba(124, 77, 255, 0.2); }
.lifeline-audience { border-color: #05ffa1; color: #05ffa1; }
.lifeline-audience:hover:not(.disabled) { box-shadow: 0 0 20px rgba(5, 255, 161, 0.4); background: rgba(5, 255, 161, 0.2); }

/* Audience Animation Modal */
.audience-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 10000; justify-content: center; align-items: center; }
.audience-content { background: linear-gradient(135deg, #1a1a2e, #16213e); border-radius: 24px; padding: 30px; text-align: center; max-width: 500px; width: 90%; border: 2px solid #05ffa1; }
.audience-content h3 { color: #05ffa1; margin-bottom: 20px; }
.audience-votes { display: flex; justify-content: space-around; gap: 15px; flex-wrap: wrap; margin: 20px 0; }
.vote-option { flex: 1; text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 12px; transition: all 0.3s; }
.vote-option .vote-letter { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
.vote-option .vote-bar-container { width: 100%; height: 8px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; margin: 10px 0; }
.vote-option .vote-bar { height: 100%; border-radius: 10px; transition: width 0.5s; }
.vote-option .vote-percent { font-size: 18px; font-weight: bold; }
.audience-close { background: linear-gradient(135deg, #7c4dff, #00f5ff); border: none; padding: 10px 30px; border-radius: 50px; color: #fff; cursor: pointer; margin-top: 20px; }

.difficulty-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-bottom: 12px; }
.difficulty-easy-badge { background: rgba(5, 255, 161, 0.15); color: #05ffa1; border: 1px solid rgba(5, 255, 161, 0.3); }
.difficulty-medium-badge { background: rgba(255, 165, 0, 0.15); color: #ffa500; border: 1px solid rgba(255, 165, 0, 0.3); }
.difficulty-hard-badge { background: rgba(255, 51, 102, 0.15); color: #ff3366; border: 1px solid rgba(255, 51, 102, 0.3); }

.setup-info { text-align: center; margin-bottom: 20px; padding: 12px; background: rgba(255, 255, 255, 0.03); border-radius: 12px; font-size: 14px; }
.setup-info span { color: #00f5ff; font-weight: bold; }
.game-container { max-width: 700px; width: 90%; margin: 50px auto; }
.game-title { text-align: center; margin-bottom: 20px; font-size: 34px; font-weight: 800; }
.card { background: rgba(255, 255, 255, 0.03); padding: 30px; margin-bottom: 35px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.06); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); }
label { display: flex; align-items: center; padding: 16px 20px; margin: 12px 0; border-radius: 12px; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.04); cursor: pointer; transition: all 0.2s ease; }
label:hover { background: rgba(0, 245, 255, 0.06); border-color: rgba(0, 245, 255, 0.2); transform: translateX(4px); }
label.disabled-option { opacity: 0.4; cursor: not-allowed; text-decoration: line-through; }
label.disabled-option:hover { transform: none; background: rgba(255, 255, 255, 0.02); }
input[type="radio"] { appearance: none; width: 20px; height: 20px; border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 50%; margin-right: 14px; display: grid; place-content: center; }
input[type="radio"]::before { content: ""; width: 10px; height: 10px; border-radius: 50%; transform: scale(0); transition: 0.2s transform; background: #00f5ff; }
input[type="radio"]:checked { border-color: #00f5ff; }
input[type="radio"]:checked::before { transform: scale(1); }
label:has(input[type="radio"]:checked) { background: rgba(0, 245, 255, 0.08); border-color: rgba(0, 245, 255, 0.4); }
button { background: linear-gradient(135deg, #7c4dff, #00f5ff); width: 100%; padding: 14px; border: none; border-radius: 14px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
button:hover { transform: translateY(-2px); box-shadow: 0 6px 30px rgba(124, 77, 255, 0.4); }
</style>

<!-- Lifelines Buttons -->
<div class="lifelines-container">
    <button class="lifeline-btn lifeline-fifty <?php echo ($fifty_fifty_available <= 0) ? 'disabled' : ''; ?>" 
            id="fiftyBtn" onclick="useFiftyFifty()" <?php echo ($fifty_fifty_available <= 0) ? 'disabled' : ''; ?>>
        🎲 50/50
    </button>
    <button class="lifeline-btn lifeline-audience <?php echo ($audience_available <= 0) ? 'disabled' : ''; ?>" 
            id="audienceBtn" onclick="useAudienceHelp()" <?php echo ($audience_available <= 0) ? 'disabled' : ''; ?>>
        👥 <?php echo t("audience"); ?>
    </button>
</div>

<!-- Audience Modal -->
<div id="audienceModal" class="audience-modal">
    <div class="audience-content">
        <h3>👥 <?php echo t("audience_help"); ?></h3>
        <div id="audienceVotes" class="audience-votes">
            <!-- Dynamic votes will appear here -->
        </div>
        <button class="audience-close" onclick="closeAudienceModal()"><?php echo t("close"); ?></button>
    </div>
</div>

<!-- Timer Display -->
<div class="timer-container">
    <button id="soundToggleBtn" onclick="toggleSound()">🔊</button>
    <div class="timer-box quiz-timer" id="quizTimerBox">
        <div class="timer-label"><?php echo t("total_time_left"); ?></div>
        <div class="timer-value" id="quizTimer">--:--</div>
    </div>
</div>

<div class="game-container">
    <h1 class="game-title">💻 <?php echo t("title"); ?></h1>
    
    <div class="setup-info">
        🎯 <?php echo t("difficulty"); ?>: <span><?php echo t($selected_difficulty == 'all' ? 'all_difficulties' : $selected_difficulty); ?></span> &nbsp;|&nbsp;
        📊 <?php echo t("questions"); ?>: <span><?php echo $total_questions; ?></span>
    </div>
    
    <form method="POST" id="quizForm">
        <?php foreach($questions as $index => $q): ?>
            <div class="card" id="card-<?php echo $index; ?>" data-question-index="<?php echo $index; ?>" data-correct-answer="<?php echo $q['answer']; ?>">
                <div class="difficulty-badge difficulty-<?php echo $q['difficulty']; ?>-badge">
                    <?php echo $difficulty_icons[$q['difficulty']]; ?> <?php echo t($q['difficulty']); ?>
                </div>
                
                <h3><?php echo htmlspecialchars(t($q["question"])); ?></h3>
                <?php for($opt = 1; $opt <= 4; $opt++): ?>
                <label class="option-label option-<?php echo $opt; ?>" data-opt="<?php echo $opt; ?>">
                    <input type="radio" name="q<?php echo $q['id']; ?>" value="<?php echo $opt; ?>" required>
                    <?php echo htmlspecialchars(t($q["option_".chr(96+$opt)])); ?>
                </label>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>
        
        <button type="submit" name="submit_quiz" id="submitBtn">👍 <?php echo t("submit"); ?></button>
    </form>
</div>

<script>
// ========== SOUND EFFECTS ==========
let soundEnabled = localStorage.getItem('soundEnabled') !== 'false';

function playBeep(frequency, duration, volume = 0.3) {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        oscillator.frequency.value = frequency;
        gainNode.gain.value = volume;
        oscillator.start();
        gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + duration);
        oscillator.stop(audioContext.currentTime + duration);
        if (audioContext.state === 'suspended') audioContext.resume();
    } catch(e) {}
}

function playCorrectSound() { if(soundEnabled) { playBeep(880, 0.2, 0.3); setTimeout(() => playBeep(1320, 0.3, 0.3), 150); } }
function playWrongSound() { if(soundEnabled) { playBeep(440, 0.3, 0.4); setTimeout(() => playBeep(330, 0.4, 0.4), 200); } }
function playCompleteSound() { if(soundEnabled) { playBeep(660, 0.2, 0.3); setTimeout(() => playBeep(880, 0.2, 0.3), 200); setTimeout(() => playBeep(1320, 0.5, 0.4), 400); } }
function playClickSound() { if(soundEnabled) playBeep(800, 0.08, 0.2); }
function playTimeoutSound() { if(soundEnabled) { playBeep(440, 0.5, 0.5); setTimeout(() => playBeep(330, 0.8, 0.5), 300); } }

function toggleSound() {
    soundEnabled = !soundEnabled;
    localStorage.setItem('soundEnabled', soundEnabled);
    const btn = document.getElementById('soundToggleBtn');
    if(btn) btn.innerHTML = soundEnabled ? '🔊' : '🔇';
}

// ========== TIMER ==========
let quizTimeLeft = <?php echo $total_quiz_time; ?>;
const quizTimerElement = document.getElementById('quizTimer');

function updateQuizTimer() {
    if(!quizTimerElement) return;
    let minutes = Math.floor(quizTimeLeft / 60);
    let seconds = quizTimeLeft % 60;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    quizTimerElement.textContent = minutes + ':' + seconds;
    if(quizTimeLeft <= 60) quizTimerElement.classList.add('warning');
    if(quizTimeLeft === 10) playTimeoutSound();
    if(quizTimeLeft <= 0) document.getElementById('quizForm').submit();
    quizTimeLeft--;
}
updateQuizTimer();
setInterval(updateQuizTimer, 1000);

// ========== 50/50 LIFELINE ==========
// ========== 50/50 LIFELINE ==========
let fiftyUsed = false;
let hiddenOptions = {};

function useFiftyFifty() {
    if(fiftyUsed) return;
    const fiftyBtn = document.getElementById('fiftyBtn');
    if(fiftyBtn.classList.contains('disabled')) return;
    
    playClickSound();
    fiftyUsed = true;
    fiftyBtn.classList.add('disabled');
    
    const visibleCard = getCurrentVisibleCard();
    if(!visibleCard) return;
    
    const correctAnswer = parseInt(visibleCard.dataset.correctAnswer);
    const questionIndex = visibleCard.dataset.questionIndex;
    
    if(!hiddenOptions[questionIndex]) {
        hiddenOptions[questionIndex] = [];
    }
    
    const options = visibleCard.querySelectorAll('.option-label');
    const wrongOptions = [];
    
    options.forEach(opt => {
        const optValue = parseInt(opt.dataset.opt);
        if(optValue !== correctAnswer && !hiddenOptions[questionIndex].includes(optValue)) {
            wrongOptions.push(opt);
        }
    });
    
    const toHide = wrongOptions.slice(0, 2);
    toHide.forEach(opt => {
        const optValue = parseInt(opt.dataset.opt);
        hiddenOptions[questionIndex].push(optValue);
        opt.classList.add('disabled-option');
        const radio = opt.querySelector('input[type="radio"]');
        if(radio) radio.disabled = true;
        
        opt.style.transition = 'opacity 0.3s';
        opt.style.opacity = '0.5';
        setTimeout(() => { opt.style.opacity = ''; }, 500);
    });
    
    <?php if($logged_in): ?>
    fetch('update_lifeline.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'type=fifty_fifty'
    });
    <?php endif; ?>
}

function getCurrentVisibleCard() {
    const cards = document.querySelectorAll('.card');
    for(let card of cards) {
        if(card.style.display !== 'none') {
            return card;
        }
    }
    return document.querySelector('.card');
}

function applyHiddenOptionsForQuestion(questionIndex) {
    const card = document.querySelector(`.card[data-question-index="${questionIndex}"]`);
    if(!card) return;
    
    const allOptions = card.querySelectorAll('.option-label');
    allOptions.forEach(opt => {
        opt.classList.remove('disabled-option');
        const radio = opt.querySelector('input[type="radio"]');
        if(radio) radio.disabled = false;
        opt.style.opacity = '';
    });
    
    if(hiddenOptions[questionIndex]) {
        hiddenOptions[questionIndex].forEach(optValue => {
            const optionToHide = card.querySelector(`.option-label[data-opt="${optValue}"]`);
            if(optionToHide) {
                optionToHide.classList.add('disabled-option');
                const radio = optionToHide.querySelector('input[type="radio"]');
                if(radio) radio.disabled = true;
                optionToHide.style.opacity = '0.5';
            }
        });
    }
}

// Modify the question switching to apply hidden options
const originalShowQuestion = window.showQuestion;
window.showQuestion = function(index) {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, i) => {
        card.style.display = i === index ? 'block' : 'none';
        if(i === index) {
            applyHiddenOptionsForQuestion(i);
        }
    });
    if(originalShowQuestion) originalShowQuestion(index);
};

// ========== AUDIENCE HELP ==========
function useAudienceHelp() {
    const audienceBtn = document.getElementById('audienceBtn');
    if(audienceBtn.classList.contains('disabled')) return;
    
    playClickSound();
    audienceBtn.classList.add('disabled');
    
    const visibleCard = document.querySelector('.card:not([style*="display: none"])') || document.querySelector('.card');
    if(!visibleCard) return;
    
    const correctAnswer = parseInt(visibleCard.dataset.correctAnswer);
    
    // Generate random audience votes
    let votes = {1: 0, 2: 0, 3: 0, 4: 0};
    let totalVotes = 100;
    
    // Correct answer gets majority (40-70%)
    votes[correctAnswer] = Math.floor(Math.random() * 30) + 40;
    let remaining = totalVotes - votes[correctAnswer];
    
    // Distribute remaining votes among wrong answers
    const wrongAnswers = [1,2,3,4].filter(v => v !== correctAnswer);
    for(let i = 0; i < wrongAnswers.length - 1; i++) {
        votes[wrongAnswers[i]] = Math.floor(Math.random() * (remaining - 5));
        remaining -= votes[wrongAnswers[i]];
    }
    votes[wrongAnswers[wrongAnswers.length - 1]] = remaining;
    
    // Shuffle for display
    const options = ['A', 'B', 'C', 'D'];
    const optionTexts = [];
    visibleCard.querySelectorAll('.option-label').forEach((opt, idx) => {
        const optValue = parseInt(opt.dataset.opt);
        const text = opt.querySelector('input[type="radio"]')?.nextSibling?.nodeValue?.trim() || opt.innerText.replace(/[A-D]\.\s*/, '');
        optionTexts[optValue] = `${options[optValue-1]}. ${text}`;
    });
    
    // Show modal with votes
    const modal = document.getElementById('audienceModal');
    const votesContainer = document.getElementById('audienceVotes');
    votesContainer.innerHTML = '';
    
    for(let i = 1; i <= 4; i++) {
        const percentage = votes[i];
        const color = (i === correctAnswer) ? '#05ffa1' : '#ffa500';
        const barColor = (i === correctAnswer) ? '#05ffa1' : '#7c4dff';
        
        votesContainer.innerHTML += `
            <div class="vote-option">
                <div class="vote-letter" style="color: ${color};">${options[i-1]}</div>
                <div class="vote-bar-container">
                    <div class="vote-bar" style="width: ${percentage}%; background: ${barColor};"></div>
                </div>
                <div class="vote-percent" style="color: ${color};">${percentage}%</div>
                <div style="font-size: 11px; color: #9ca3af; margin-top: 5px;">${optionTexts[i]?.substring(0, 40) || ''}</div>
            </div>
        `;
    }
    
    modal.style.display = 'flex';
    
    // Update database
    <?php if($logged_in): ?>
    fetch('update_lifeline.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'type=audience'
    });
    <?php endif; ?>
}

function closeAudienceModal() {
    document.getElementById('audienceModal').style.display = 'none';
}

// Close modal on outside click
document.getElementById('audienceModal')?.addEventListener('click', function(e) {
    if(e.target === this) closeAudienceModal();
});

// ========== SUBMIT SOUND ==========
document.getElementById('quizForm')?.addEventListener('submit', () => playCompleteSound());

// ========== PREVENT ENTER KEY ==========
document.addEventListener('keypress', function(e) {
    if(e.key === 'Enter') { e.preventDefault(); return false; }
});

// ========== INIT SOUND BUTTON ==========
document.getElementById('soundToggleBtn') && (document.getElementById('soundToggleBtn').innerHTML = soundEnabled ? '🔊' : '🔇');

// ========== RESUME AUDIO CONTEXT ==========
document.body.addEventListener('click', function initAudio() {
    try { new (window.AudioContext || window.webkitAudioContext)().resume(); } catch(e) {}
    document.body.removeEventListener('click', initAudio);
}, { once: true });
</script>

<?php include "footer.php"; ?>
<?php
include_once "db.php";
include_once "lang.php";

// Ստուգել եթե session-ը դեռ չի սկսվել
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET["lang"])){
    $_SESSION["lang"] = $_GET["lang"];
    $lang = $_GET["lang"];
} elseif(isset($_SESSION["lang"])){
    $lang = $_SESSION["lang"];
} else {
    $lang = "en";
}

// Ստուգել արդյոք օգտատերը մուտք է գործել
$logged_in = isset($_SESSION["user_id"]);
$is_admin = isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true;
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "";
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : t("title"); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top-bar">
    <div class="lang-switch-container" style="text-align: center; margin-top: 20px;">
        <div class="lang-switch" style="display: inline-flex;">
            <a href="?lang=en" class="lang-btn <?php echo ($lang == 'en') ? 'active' : ''; ?>">🇬🇧 EN</a>
            <a href="?lang=hy" class="lang-btn <?php echo ($lang == 'hy') ? 'active' : ''; ?>">🇦🇲 HY</a>
        </div>
    </div>
    
    <div class="user-menu" style="text-align: center; margin-top: 15px;">
        <!-- Theme Toggle Button -->
        <button id="themeToggleBtn" class="theme-toggle-btn" onclick="toggleTheme()">🌙</button>
        
        <?php if($logged_in): ?>
            <span style="color: #00f5ff;">👤 <?php echo htmlspecialchars($username); ?></span>
            <?php if($is_admin): ?>
                <span style="background: #ff3366; padding: 2px 8px; border-radius: 20px; font-size: 12px; margin-left: 10px;">Admin</span>
            <?php endif; ?>
            <a href="logout.php?lang=<?php echo $lang; ?>" class="lang-btn" style="margin-left: 15px;">🚪 <?php echo t("logout"); ?></a>
        <?php else: ?>
            <a href="login.php?lang=<?php echo $lang; ?>" class="lang-btn">🔐 <?php echo t("login"); ?></a>
            <a href="register.php?lang=<?php echo $lang; ?>" class="lang-btn">📝 <?php echo t("register"); ?></a>
        <?php endif; ?>
    </div>
</div>

<script>
// ========== DARK/LIGHT MODE ==========
// Ստուգել պահված թեման localStorage-ում
let isDarkMode = localStorage.getItem('theme') !== 'light';

function toggleTheme() {
    isDarkMode = !isDarkMode;
    const themeBtn = document.getElementById('themeToggleBtn');
    
    if(isDarkMode) {
        document.body.classList.remove('light-mode');
        themeBtn.innerHTML = '🌙';
        themeBtn.title = 'Dark Mode';
        localStorage.setItem('theme', 'dark');
    } else {
        document.body.classList.add('light-mode');
        themeBtn.innerHTML = '☀️';
        themeBtn.title = 'Light Mode';
        localStorage.setItem('theme', 'light');
    }
}

function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    const themeBtn = document.getElementById('themeToggleBtn');
    
    if(savedTheme === 'light') {
        isDarkMode = false;
        document.body.classList.add('light-mode');
        if(themeBtn) themeBtn.innerHTML = '☀️';
    } else {
        isDarkMode = true;
        document.body.classList.remove('light-mode');
        if(themeBtn) themeBtn.innerHTML = '🌙';
    }
}

// Initialize theme on page load
initTheme();
</script>
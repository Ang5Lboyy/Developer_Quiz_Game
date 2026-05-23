<?php
$page_title = "Login";
include "header.php";

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    
    if(empty($username) || empty($password)) {
        $error = t("all_fields_required");
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($user = $result->fetch_assoc()) {
    // Ուղղակի համեմատում (առանց password_verify)
    if($password == $user["password"]) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["is_admin"] = ($user["is_admin"] == 1);
        
        header("Location: index.php?lang=$lang");
        exit;
    } else {
        $error = t("invalid_credentials");
    }
} else {
    $error = t("invalid_credentials");
}
        $stmt->close();
    }
}
?>

<div class="auth-container">
    <h1 class="auth-title">🔐 <?php echo t("login"); ?></h1>
    
    <?php if($error): ?>
        <div class="error-box" style="background: rgba(255,51,102,0.1); border: 1px solid #ff3366; border-radius: 12px; padding: 12px; margin-bottom: 20px; color: #ff3366; text-align: center;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="text" name="username" placeholder="<?php echo t("username_or_email"); ?>" required>
        <input type="password" name="password" placeholder="<?php echo t("password"); ?>" required>
        
        <button type="submit" name="login">🔓 <?php echo t("login"); ?></button>
    </form>
    
    <p style="text-align: center; margin-top: 20px; color: #9ca3af;">
        <?php echo t("no_account"); ?> 
        <a href="register.php?lang=<?php echo $lang; ?>" style="color: #00f5ff;"><?php echo t("register_here"); ?></a>
    </p>
    
    <a href="index.php?lang=<?php echo $lang; ?>" class="button admin" style="display: block; text-align: center;">⬅ <?php echo t("back_home"); ?></a>
</div>

<?php include "footer.php"; ?>
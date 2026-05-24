<?php
$page_title = "Register";
include "header.php";

$error = "";
$success = "";

// Ստուգել արդյոք կա արդեն որևէ օգտատեր
$check_users = $conn->query("SELECT COUNT(*) as count FROM users");
$user_count = $check_users->fetch_assoc()["count"];
$first_user = ($user_count == 0);

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Վալիդացիա
    if(empty($username) || empty($email) || empty($password)) {
        $error = t("all_fields_required");
    } elseif(strlen($username) < 3) {
        $error = t("username_min_length");
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = t("invalid_email");
    } elseif(strlen($password) < 6) {
        $error = t("password_min_length");
    } elseif($password !== $confirm_password) {
        $error = t("passwords_not_match");
    } else {
        // Ստուգել username-ի և email-ի ունիկալությունը
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();
        
        if($check->num_rows > 0) {
            $error = t("username_or_email_exists");
        } else {
    // Գաղտնաբառը պահում ենք ինչպես կա (ԱՆՎՏԱՆԳ - միայն սովորելու համար)
    $hashed_password = $password; // ԱՌԱՆՑ HASH-ի
    $is_admin = $first_user ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $email, $hashed_password, $is_admin);
    
    if($stmt->execute()) {
                if($first_user) {
                    $success = t("admin_registered_success");
                } else {
                    $success = t("registered_success");
                }
                // Ավտոմատ մուտք գործել
                $_SESSION["user_id"] = $stmt->insert_id;
                $_SESSION["username"] = $username;
                $_SESSION["is_admin"] = ($is_admin == 1);
                
                header("refresh:2;url=index.php?lang=$lang");
            } else {
                $error = t("registration_error");
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<div class="auth-container">
    <h1 class="auth-title">📝 <?php echo t("register_as_admin"); ?></h1>
    
    <?php if($first_user): ?>
        <div class="info-box" style="background: rgba(0,245,255,0.1); border: 1px solid #00f5ff; border-radius: 12px; padding: 15px; margin-bottom: 20px; text-align: center;">
            🎯 <?php echo t("first_user_admin"); ?>
        </div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="error-box" style="background: rgba(255,51,102,0.1); border: 1px solid #ff3366; border-radius: 12px; padding: 12px; margin-bottom: 20px; color: #ff3366; text-align: center;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="success-box" style="background: rgba(5,255,161,0.1); border: 1px solid #05ffa1; border-radius: 12px; padding: 12px; margin-bottom: 20px; color: #05ffa1; text-align: center;">
            <?php echo $success; ?> 🎉
        </div>
    <?php endif; ?>
    
    <form method="POST" style="display: flex; flex-direction: column; gap: 12px;">
        <input type="text" name="username" placeholder="<?php echo t("username"); ?>" required 
               style="width: 100%; padding: 14px 18px; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; background: rgba(255,255,255,0.04); color: #fff; font-size: 15px; outline: none; box-sizing: border-box;">
        
        <input type="email" name="email" placeholder="<?php echo t("email"); ?>" required 
               style="width: 100%; padding: 14px 18px; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; background: rgba(255,255,255,0.04); color: #fff; font-size: 15px; outline: none; box-sizing: border-box;">
        
        <input type="password" name="password" placeholder="<?php echo t("password"); ?>" required 
               style="width: 100%; padding: 14px 18px; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; background: rgba(255,255,255,0.04); color: #fff; font-size: 15px; outline: none; box-sizing: border-box;">
        
        <input type="password" name="confirm_password" placeholder="<?php echo t("confirm_password"); ?>" required 
               style="width: 100%; padding: 14px 18px; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; background: rgba(255,255,255,0.04); color: #fff; font-size: 15px; outline: none; box-sizing: border-box;">
        
        <button type="submit" name="register" 
                style="background: linear-gradient(135deg, #7c4dff, #00f5ff); box-shadow: 0 4px 20px rgba(124,77,255,0.2); width: 100%; padding: 14px; border: none; border-radius: 14px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 10px;">
            ✅ <?php echo t("register"); ?>
        </button>
    </form>
    
    <p style="text-align: center; margin-top: 20px; color: #9ca3af;">
        <?php echo t("already_have_account"); ?> 
        <a href="login.php?lang=<?php echo $lang; ?>" style="color: #00f5ff; text-decoration: none;"><?php echo t("login_here"); ?></a>
    </p>
    
    <a href="index.php?lang=<?php echo $lang; ?>" class="button admin" style="display: block; text-align: center; margin-top: 15px; text-decoration: none;">⬅ <?php echo t("back_home"); ?></a>
</div>

<?php include "footer.php"; ?>
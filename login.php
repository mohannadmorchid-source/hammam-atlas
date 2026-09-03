<?php
// login.php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_currency'] = $user['currency'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Credenziali errate.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Accedi - Hammam Atlas</title>
</head>
<body style="font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4;">
    <form method="POST" style="background: white; padding: 30px; border-radius: 8px; width: 300px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Accedi</h2><br>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p><br>"; ?>
        <input type="text" name="username" placeholder="Username" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
        <button type="submit" style="width: 100%; padding: 10px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer;">Entra</button>
        <br><br>
        <a href="index.php">Torna alla Home</a>
    </form>
</body>
</html>
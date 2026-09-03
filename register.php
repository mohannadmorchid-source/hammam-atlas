<?php
// register.php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $country = $_POST['country'];
    $currency = getCurrencyByCountry($country);

    try {
        $stmt = $db->prepare("INSERT INTO users (username, password, country, currency) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $country, $currency]);

        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['user_currency'] = $currency;

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $error = "Nome utente già esistente.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione - Hammam Atlas</title>
</head>
<body style="font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4;">
    <form method="POST" style="background: white; padding: 30px; border-radius: 8px; width: 320px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Registrazione</h2><br>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p><br>"; ?>
        <input type="text" name="username" placeholder="Username" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <label style="font-size: 14px;">Seleziona Paese (per impostare la valuta):</label>
        <select name="country" style="width: 100%; padding: 8px; margin: 5px 0 15px;">
            <option value="IT">(€)</option>
            <option value="US">($)</option>
            <option value="UAH">(₴)</option>
        </select>
        <button type="submit" style="width: 100%; padding: 10px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer;">Crea Account</button>
        <br><br>
        <a href="index.php">Torna alla Home</a>
    </form>
</body>
</html>
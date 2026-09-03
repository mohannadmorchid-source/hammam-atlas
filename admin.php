<?php
// admin.php
require_once __DIR__ . '/config.php';

// Accesso tramite parola chiave
if (isset($_POST['secret_key'])) {
    if ($_POST['secret_key'] === ADMIN_SECRET_KEY) {
        $_SESSION['is_admin'] = true;
    } else {
        $error = "Parola chiave non corretta!";
    }
}

// Inserimento nuovi prodotti
if (isset($_SESSION['is_admin']) && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = (float)$_POST['price'];
    $catId = (int)$_POST['category_id'];

    $imgPath = '';
    if (!empty($_FILES['image']['name'])) {
        $imgPath = 'uploads/' . time() . '_' . $_FILES['image']['name'];
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        move_uploaded_file($_FILES['image']['tmp_name'], $imgPath);
    }

    $stmt = $db->prepare("INSERT INTO products (category_id, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$catId, $name, $desc, $price, $imgPath]);
    $success = "Prodotto caricato con successo!";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pannello Amministratore</title>
</head>
<body style="font-family: sans-serif; padding: 40px; background: #f4f4f4;">

<?php if (!isset($_SESSION['is_admin'])): ?>
    <form method="POST" style="max-width: 320px; margin: 100px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h3>Area Protetta Admin</h3><br>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p><br>"; ?>
        <input type="password" name="secret_key" placeholder="Inserisci Parola Chiave" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
        <button type="submit" style="width: 100%; padding: 10px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer;">Accedi</button>
    </form>
<?php else: ?>
    <h2>Gestione Catalogo - Hammam Atlas</h2>
    <br>
    <a href="index.php">Torna al Sito</a> | <a href="logout.php">Disconnetti Admin</a>
    <br><br>

    <?php if (isset($success)) echo "<p style='color:green;'>$success</p><br>"; ?>

    <form method="POST" enctype="multipart/form-data" style="max-width: 500px; background: white; padding: 25px; border-radius: 8px;">
        <h3>Aggiungi Nuovo Prodotto</h3><br>

        <label>Nome Prodotto:</label>
        <input type="text" name="name" required style="width: 100%; padding: 8px; margin: 5px 0 10px;">

        <label>Categoria:</label>
        <select name="category_id" style="width: 100%; padding: 8px; margin: 5px 0 10px;">
            <?php
            $cats = $db->query("SELECT * FROM categories")->fetchAll();
            foreach ($cats as $c) {
                echo "<option value='{$c['id']}'>{$c['name']}</option>";
            }
            ?>
        </select>

        <label>Prezzo Base (€):</label>
        <input type="number" step="0.01" name="price" required style="width: 100%; padding: 8px; margin: 5px 0 10px;">

        <label>Descrizione:</label>
        <textarea name="description" required style="width: 100%; padding: 8px; margin: 5px 0 10px; height: 90px;"></textarea>

        <label>Immagine Prodotto:</label>
        <input type="file" name="image" accept="image/*" required style="margin: 5px 0 15px; display: block;">

        <button type="submit" name="add_product" style="width: 100%; padding: 10px; background: #e67e22; color: white; border: none; border-radius: 4px; cursor: pointer;">Salva Prodotto</button>
    </form>
<?php endif; ?>

</body>
</html>
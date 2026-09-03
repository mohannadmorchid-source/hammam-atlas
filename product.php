<?php
// product.php
require_once __DIR__ . '/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Prodotto non trovato.";
    exit;
}

// Inserimento recensione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $userName = $_SESSION['username'] ?? ($_POST['user_name'] ?: 'Anonimo');
    $rating = (int)$_POST['rating'];
    $comment = $_POST['comment'];
    $imagePath = '';

    if (!empty($_FILES['review_image']['name'])) {
        $imagePath = 'uploads/rev_' . time() . '_' . $_FILES['review_image']['name'];
        move_uploaded_file($_FILES['review_image']['tmp_name'], $imagePath);
    }

    $stmtRev = $db->prepare("INSERT INTO reviews (product_id, user_name, rating, comment, image) VALUES (?, ?, ?, ?, ?)");
    $stmtRev->execute([$id, $userName, $rating, $comment, $imagePath]);
    header("Location: product.php?id=$id");
    exit;
}

// Lettura recensioni
$stmtRevGet = $db->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$stmtRevGet->execute([$id]);
$reviews = $stmtRevGet->fetchAll();

// Calcolo media stelle
$avgRating = 0;
if (count($reviews) > 0) {
    $sum = array_sum(array_column($reviews, 'rating'));
    $avgRating = round($sum / count($reviews), 1);
}

// Recupero Prodotti Consigliati
$stmtRec = $db->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 3");
$stmtRec->execute([$product['category_id'], $id]);
$recommended = $stmtRec->fetchAll();

$msg = urlencode("Ciao! Vorrei acquistare: " . $product['name'] . " (Prezzo: " . number_format($product['price'], 2) . " " . getUserCurrency() . ")");
?>

<style>
    .product-detail-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 40px;
    }
    .product-detail-img {
        flex: 1 1 300px;
    }
    .product-detail-img img {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }
    .product-detail-info {
        flex: 1 1 300px;
    }
</style>

<div class="product-detail-container">
    <div class="product-detail-img">
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='https://via.placeholder.com/400'">
    </div>
    <div class="product-detail-info">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <div class="price" style="font-size: 24px;"><?= number_format($product['price'], 2) ?> <?= getUserCurrency() ?></div>
        <p><strong>Valutazione:</strong> <?= $avgRating ?> / 5 ⭐ (<?= count($reviews) ?> recensioni)</p>
        <br>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <br>
        
        <form action="cart.php" method="POST" style="margin-bottom: 15px;">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <button type="submit" class="btn" style="width: 100%;">Aggiungi al Carrello 🛒</button>
        </form>

        <p><strong>Oppure acquista via chat:</strong></p><br>
        <div style="display: flex; gap: 10px;">
            <a href="https://wa.me/390000000000?text=<?= $msg ?>" target="_blank" class="btn btn-contact" style="flex: 1;">WhatsApp 💬</a>
            <a href="https://t.me/il_tuo_username?text=<?= $msg ?>" target="_blank" class="btn btn-telegram" style="flex: 1;">Telegram ✈️</a>
        </div>
    </div>
</div>

<hr style="border-color: var(--border-color);"><br>

<h3>Prodotti Consigliati</h3>
<br>
<div class="product-grid">
    <?php foreach ($recommended as $rec): ?>
        <div class="product-card">
            <a href="product.php?id=<?= $rec['id'] ?>" style="text-decoration: none; color: inherit;">
                <img src="<?= htmlspecialchars($rec['image']) ?>" onerror="this.src='https://via.placeholder.com/200'">
                <h4><?= htmlspecialchars($rec['name']) ?></h4>
                <div class="price"><?= number_format($rec['price'], 2) ?> <?= getUserCurrency() ?></div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<br><hr style="border-color: var(--border-color);"><br>

<h3>Recensioni Utenti</h3>
<br>

<form action="" method="POST" enctype="multipart/form-data" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); max-width: 500px;">
    <h4>Lascia una recensione</h4><br>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <input type="text" name="user_name" placeholder="Il tuo nome" required style="width: 100%; margin-bottom: 10px; padding: 8px;">
    <?php endif; ?>
    <label>Voto:</label>
    <select name="rating" style="width: 100%; margin: 5px 0 10px; padding: 8px;">
        <option value="5">5 Stelle ⭐⭐⭐⭐⭐</option>
        <option value="4">4 Stelle ⭐⭐⭐⭐</option>
        <option value="3">3 Stelle ⭐⭐⭐</option>
        <option value="2">2 Stelle ⭐⭐</option>
        <option value="1">1 Stella ⭐</option>
    </select>
    <textarea name="comment" placeholder="Scrivi la tua recensione..." required style="width: 100%; margin-bottom: 10px; padding: 8px; height: 80px;"></textarea>
    <label>Aggiungi Immagine (opzionale):</label>
    <input type="file" name="review_image" accept="image/*" style="margin: 5px 0 15px; display: block; width: 100%;">
    <button type="submit" name="submit_review" class="btn" style="width: 100%;">Invia Recensione</button>
</form>

<br>
<div>
    <?php foreach ($reviews as $r): ?>
        <div style="background: #fff; padding: 15px; border-radius: 5px; border: 1px solid var(--border-color); margin-bottom: 10px;">
            <strong><?= htmlspecialchars($r['user_name']) ?></strong> - <?= str_repeat('⭐', $r['rating']) ?>
            <p style="margin-top: 5px;"><?= htmlspecialchars($r['comment']) ?></p>
            <?php if (!empty($r['image'])): ?>
                <br><img src="<?= htmlspecialchars($r['image']) ?>" style="max-width: 150px; border-radius: 5px;">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</main>
</div>
</body>
</html>
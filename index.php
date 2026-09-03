<?php
// index.php
require_once __DIR__ . '/header.php';

$search = $_GET['q'] ?? '';
$category = $_GET['cat'] ?? null;

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE :q OR description LIKE :q)";
    $params[':q'] = "%$search%";
}

if ($category) {
    $query .= " AND category_id = :cat";
    $params[':cat'] = $category;
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!-- Cornice Iniziale Promozionale -->
<div class="banner">
    <h2>Benvenuti su Hammam Atlas</h2>
    <p>Scopri i migliori prodotti tradizionali per la cura del corpo e il benessere autentico.</p>
</div>

<h2>Catalogo Prodotti</h2>
<br>

<div class="product-grid">
    <?php if (empty($products)): ?>
        <p>Nessun prodotto trovato.</p>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a href="product.php?id=<?= $p['id'] ?>" style="text-decoration: none; color: inherit;">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" onerror="this.src='https://via.placeholder.com/200'">
                    <h4 style="margin-top: 10px;"><?= htmlspecialchars($p['name']) ?></h4>
                    <div class="price"><?= number_format($p['price'], 2) ?> <?= getUserCurrency() ?></div>
                </a>
                <a href="product.php?id=<?= $p['id'] ?>" class="btn">Visualizza</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</main>
</div>
</body>
</html>
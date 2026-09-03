<?php
// cart.php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $pid = (int)$_POST['product_id'];
    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;
}

$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    if (!empty($ids)) {
        $stmt = $db->query("SELECT * FROM products WHERE id IN ($ids)");
        $products = $stmt->fetchAll();

        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
            $cartItems[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
        }
    }
}
?>

<h2>Il tuo Carrello</h2>
<br>

<?php if (empty($cartItems)): ?>
    <p>Il carrello è vuoto.</p>
<?php else: ?>
    <table style="width: 100%; border-collapse: collapse; background: white;">
        <thead>
            <tr style="background: #eee; text-align: left;">
                <th style="padding: 10px;">Prodotto</th>
                <th style="padding: 10px;">Prezzo</th>
                <th style="padding: 10px;">Quantità</th>
                <th style="padding: 10px;">Totale</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;"><?= htmlspecialchars($item['product']['name']) ?></td>
                    <td style="padding: 10px;"><?= number_format($item['product']['price'], 2) ?> <?= getUserCurrency() ?></td>
                    <td style="padding: 10px;"><?= $item['qty'] ?></td>
                    <td style="padding: 10px;"><?= number_format($item['subtotal'], 2) ?> <?= getUserCurrency() ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <h3>Totale: <?= number_format($total, 2) ?> <?= getUserCurrency() ?></h3>
    <br>
    <?php
    $orderMsg = "Ciao! Vorrei completare il seguente ordine:\n";
    foreach ($cartItems as $item) {
        $orderMsg .= "- " . $item['product']['name'] . " x" . $item['qty'] . "\n";
    }
    $orderMsg .= "Totale: " . number_format($total, 2) . " " . getUserCurrency();
    $whatsappLink = "https://wa.me/390000000000?text=" . urlencode($orderMsg);
    ?>
    <a href="<?= $whatsappLink ?>" target="_blank" class="btn btn-contact">Invia Ordine via WhatsApp 💬</a>
<?php endif; ?>

</main>
</div>
</body>
</html>
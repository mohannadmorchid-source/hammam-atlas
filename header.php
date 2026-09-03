<?php
// header.php
require_once __DIR__ . '/config.php';
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hammam Atlas</title>
    
    <link rel="icon" type="image/jpeg" href="uploads/logo.jpg">

    <style>
        :root {
            --bg-color: #F8F5F0;          
            --header-bg: #221A15;         
            --primary-gold: #B38738;      
            --accent-bronze: #8C5E35;     
            --text-dark: #2B211A;         
            --card-bg: #FFFFFF;           
            --aside-bg: #EFE8DC;          
            --border-color: #D8C8B0;      
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Georgia, serif; }
        body { display: flex; flex-direction: column; min-height: 100vh; background: var(--bg-color); color: var(--text-dark); }
        
        /* Header Adattivo */
        header { 
            display: grid; 
            grid-template-columns: 1fr auto 1fr; 
            align-items: center; 
            padding: 10px 20px; 
            background: var(--header-bg); 
            border-bottom: 2px solid var(--primary-gold); 
            position: relative;
        }

        /* Logo Ingrandito e Centrato */
        .logo-container {
            grid-column: 2;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo { display: flex; align-items: center; text-decoration: none; }
        .logo img { 
            height: 75px; 
            width: auto; 
            border-radius: 8px; 
            border: 2px solid var(--primary-gold); 
            box-shadow: 0 4px 12px rgba(179, 135, 56, 0.25);
            transition: transform 0.3s ease;
        }
        .logo img:hover { transform: scale(1.03); }

        /* Menu Utente a Destra */
        .nav-links { 
            grid-column: 3;
            display: flex; 
            align-items: center; 
            justify-content: flex-end;
            gap: 12px; 
        }
        .nav-links a { 
            color: #F5EBE6; 
            text-decoration: none; 
            font-size: 13px; 
            font-weight: 500; 
            transition: 0.3s; 
            white-space: nowrap;
        }
        .nav-links a:hover { color: var(--primary-gold); }

        /* Barra di Ricerca Comparabile (Sinistra) */
        .search-container {
            grid-column: 1;
            display: flex;
            align-items: center;
            position: relative;
        }
        .search-icon-btn {
            background: none;
            border: none;
            color: var(--primary-gold);
            font-size: 22px;
            cursor: pointer;
            padding: 5px;
        }
        
        .search-form {
            display: none;
            position: absolute;
            left: 40px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--header-bg);
            border: 1px solid var(--primary-gold);
            border-radius: 5px;
            padding: 4px;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .search-form.active { display: flex; }
        .search-form input { 
            padding: 6px 10px; 
            border: none; 
            border-radius: 3px 0 0 3px; 
            width: 160px; 
            outline: none; 
            background: #FFF;
            font-size: 14px;
        }
        .search-form button { 
            padding: 6px 10px; 
            border: none; 
            background: var(--accent-bronze); 
            color: white; 
            border-radius: 0 3px 3px 0; 
            cursor: pointer; 
        }

        /* Layout Principale Responsive */
        .container { 
            display: flex; 
            flex-direction: row; 
            flex: 1; 
        }
        aside { 
            width: 230px; 
            background: var(--aside-bg); 
            padding: 20px; 
            border-right: 1px solid var(--border-color); 
            flex-shrink: 0;
        }
        aside h3 { margin-bottom: 15px; color: var(--header-bg); border-bottom: 2px solid var(--primary-gold); padding-bottom: 5px; }
        aside ul { list-style: none; }
        aside ul li { margin-bottom: 12px; }
        aside ul li a { text-decoration: none; color: var(--accent-bronze); font-weight: 600; transition: 0.2s; }
        aside ul li a:hover { color: var(--primary-gold); padding-left: 5px; }
        
        main { flex: 1; padding: 25px; width: 100%; }

        /* Griglia Prodotti */
        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 20px; 
        }
        .product-card { border: 1px solid var(--border-color); border-radius: 8px; padding: 15px; background: var(--card-bg); text-align: center; transition: 0.3s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(140, 94, 53, 0.2); border-color: var(--primary-gold); }
        .product-card img { max-width: 100%; height: 180px; object-fit: cover; border-radius: 6px; }
        .price { font-size: 18px; color: var(--accent-bronze); font-weight: bold; margin: 10px 0; }
        
        .btn { display: inline-block; padding: 10px 16px; background: var(--accent-bronze); color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; transition: 0.3s; font-size: 14px; text-align: center; }
        .btn:hover { background: var(--primary-gold); }
        .btn-contact { background: #25D366; }
        .btn-telegram { background: #0088cc; }

        .banner { background: linear-gradient(135deg, #221A15, #3D2B1F); color: #F5EBE6; padding: 30px 20px; border-radius: 8px; margin-bottom: 25px; text-align: center; border: 1px solid var(--primary-gold); }
        .banner h2 { color: var(--primary-gold); margin-bottom: 8px; }

        /* MEDIA QUERIES (Mobile e Tablet) */
        @media (max-width: 768px) {
            header {
                padding: 10px 15px;
            }
            .logo img {
                height: 60px;
            }
            .container { 
                flex-direction: column; 
            }
            aside { 
                width: 100%; 
                border-right: none; 
                border-bottom: 1px solid var(--border-color); 
                padding: 15px;
            }
            aside ul {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 5px;
            }
            aside ul li { margin-bottom: 0; }
            aside ul li a {
                display: inline-block;
                padding: 6px 12px;
                background: #FFF;
                border: 1px solid var(--border-color);
                border-radius: 20px;
                font-size: 13px;
            }
            main {
                padding: 15px;
            }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 12px;
            }
            .product-card img {
                height: 140px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="search-container">
        <button class="search-icon-btn" onclick="toggleSearch()" title="Cerca">🔍</button>
        <form action="index.php" method="GET" class="search-form" id="searchForm">
            <input type="text" name="q" placeholder="Cerca..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit">Vai</button>
        </form>
    </div>

    <div class="logo-container">
        <a href="index.php" class="logo">
            <img src="uploads/logo.jpg" alt="Hammam Atlas Logo" onerror="this.src='https://via.placeholder.com/75?text=HA'">
        </a>
    </div>

    <div class="nav-links">
        <a href="cart.php">🛒 (<?= $cartCount ?>)</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span style="color: var(--primary-gold); font-size: 12px;">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Esci</a>
        <?php else: ?>
            <a href="register.php">Registrati</a>
            <a href="login.php">Accedi</a>
        <?php endif; ?>
        <a href="admin.php" style="font-size: 11px; opacity: 0.8;">Admin 🔑</a>
    </div>
</header>

<script>
function toggleSearch() {
    var form = document.getElementById('searchForm');
    form.classList.toggle('active');
    if (form.classList.contains('active')) {
        form.querySelector('input').focus();
    }
}
</script>

<div class="container">
    <aside>
        <h3>Categorie</h3>
        <ul>
            <li><a href="index.php">Tutti</a></li>
            <?php
            $stmt = $db->query("SELECT * FROM categories");
            while ($cat = $stmt->fetch()):
            ?>
                <li><a href="index.php?cat=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
            <?php endwhile; ?>
        </ul>
    </aside>
    <main>
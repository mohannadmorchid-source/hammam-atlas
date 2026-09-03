<?php
// config.php
session_start();

// Connessione al database SQLite (crea automaticamente il file hammam_atlas.db)
$db = new PDO('sqlite:' . __DIR__ . '/hammam_atlas.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Configurazione Amministratore (Parola chiave per accedere ad admin.php)
define('ADMIN_SECRET_KEY', 'Atlas2026Secret!');

// Inizializzazione Tabelle Database
$db->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT,
    country TEXT,
    currency TEXT
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER,
    name TEXT,
    description TEXT,
    price REAL,
    image TEXT,
    FOREIGN KEY(category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER,
    user_name TEXT,
    rating INTEGER,
    comment TEXT,
    image TEXT,
    FOREIGN KEY(product_id) REFERENCES products(id)
);
");

// Inserimento Categorie Predefinite
$db->exec("INSERT OR IGNORE INTO categories (id, name) VALUES 
(1, 'Saponi Naturali'), 
(2, 'Oli Essenziali'), 
(3, 'Accessori Hammam')");

// Mappatura Paese -> Valuta
function getCurrencyByCountry($country) {
    $currencies = [
        'IT' => '€',
        'CH' => 'CHF',
        'US' => '$',
        'MA' => 'MAD',
        'FR' => '€'
    ];
    return $currencies[$country] ?? '€';
}

function getUserCurrency() {
    return $_SESSION['user_currency'] ?? '€';
}
?>
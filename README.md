# 🍫 Cks – Gestion de stock pour caisse café au travail

Cks est une application web PHP permettant de gérer un stock de produits, des utilisateurs et leurs commandes. Développée pour un environnement professionnel, elle propose une interface complète d'administration et une expérience utilisateur simplifiée.

## ✨ Fonctionnalités principales

- ✅ Authentification avec rôles (admin / utilisateur)
- 📦 Gestion du stock : ajout, édition, suppression de produits
- 📸 Upload d'images produit
- 🛍️ Interface de commande simple pour les utilisateurs (type snack/bar)
- 🧮 Calcul automatique du total dû (sans paiement intégré)
- 🔒 Espace admin sécurisé (CSRF token, vérifications serveur)
- 🧾 Système de logs des actions administratives
- 📱 Responsive design pour usage desktop et mobile

## 📂 Structure du projet

- `index.php` : accueil avec dernières actualités
- `admin/` : interface d'administration (stock, utilisateurs, commandes, logs)
- `user/` : tableau de bord utilisateur
- `order/` : logique de commande
- `news/` : actualités
- `includes/` : en-têtes, footers, fonctions communes
- `config/` : configuration base de données et environnement
- `uploads/` : fichiers images produits

## 🔧 Installation

1. Cloner le dépôt :
```bash
git clone https://github.com/Alxndr06/Cks.git
```

2. Configurer la base de données :
- Créer une base MariaDB
- Importer le schéma depuis `db/cks_db.sql`

3. Créer le fichier de connexion à la base :
> ⚠️ Le fichier `config/db_connect.php` n'est pas inclus pour des raisons de sécurité. Créez-le manuellement :

```php
<?php
$host = 'localhost';
$dbname = 'cks_db';
$user = 'root';
$password = 'votre_mot_de_passe';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

4. Lancer l'application en local :
```bash
php -S localhost:8000
```

## 🛠️ Technologies utilisées

- PHP (vanilla)
- MariaDB
- HTML / CSS / JavaScript (vanilla)
- Composer (`vlucas/phpdotenv`)
- Git

---

> Projet développé par **Alexander Aulong**.  
> Ce projet n'est pas sous licence open source. Toute réutilisation ou distribution est interdite sans autorisation explicite.

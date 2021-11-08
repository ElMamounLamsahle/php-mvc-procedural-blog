<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="auther" content="El Mamoun Lamsahle">
        <meta name="description" content="TP#2 : Réalisation d'un blog multi-usagers avec PHP MySQL">
        <link rel="preconnect" href="https://fonts.gstatic.com" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Halant:wght@400;500;600;700&family=Nunito+Sans:wght@400;600;700;800&display=swap" />
        <link rel="stylesheet" type="text/css" href="assets/css/main.css?v=3.5" />
        <title><?= $titrePage ?></title>
    </head>
    <body>
        <header>
            <div class="grid title-bar">
                <h1>TP#2 - Blog multi-usagers</h1>
                <div class="grid user">
                    <?php if (isset($_SESSION["usager"])) : ?>
                        <span class="user">Bienvenue <?= $_SESSION["nom-usager"] ?></span>
                        <a class="btn" href="index.php?commande=logout">Se déconnecter</a>
                    <?php else : ?>
                        <?php if (strpos($_commande, "login") === false) : ?>
                            <a class="btn" href="index.php?commande=login-form">Se connecter</a>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            </div>
            <div class="grid menu-bar">
                <nav>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <?php if (isset($_SESSION["usager"])) : ?>
                            <li><a href="index.php?commande=articles-usager">Mes articles</a></li>
                            <?php if (strpos($_commande, "articles-form") === false) : ?>
                                <li><a href="index.php?commande=articles-form-ajout">Nouvel article</a></li>
                            <?php endif ?>
                        <?php endif ?>
                    </ul>
                </nav>
                <?php if (isset($barRechercheHeader)) require_once("articles-form-recherche.php") ?>
            </div>
        </header>
        <main>
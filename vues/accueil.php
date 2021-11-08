<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue d'accueil du site, si une redirection est faite avec un message d'erreur
    seul le message d'erreur est affiché avec un titre 'Erreur", si non et s'il n'y a aucun
    article est enregistré dans la base de données un autre message est affiché.
    Si non la liste des articles est affichée. -->

<?php if (isset($_SESSION["alerte"])) : ?>
    <h2>Erreur</h2>
    <div class="container">
        <p class="alert alert-<?= $_SESSION["alerte-type"] ?>"><?= $_SESSION["alerte"] ?></p>
        <div class="validation">
            <a class="btn" href="index.php">Retour à l'accueil</a>
        </div>
    </div>
<?php else : ?>
    <h2>Accueil</h2>
    <?php if ($donnees["articles"]) : ?>
        <?php require_once("articles-liste.php") ?>
    <?php else : ?>
        <?php if (isset($_SESSION["usager"])) : ?>
            <p class="alert alert-error">La base de données ne contient actuellement aucun article, voulez vous
                <a class="alert-link" href="index.php?commande=articles-form-ajout">ajouter un nouvel article</a> ?
            </p>
        <?php else : ?>
            <p class="alert alert-error">La base de données ne contient actuellement aucun article, Si vous êtes déja membre, veuillez
                <a class="alert-link" href="index.php?commande=login-form">se connecter</a> pour ajouter un nouvel article.
            </p>
        <?php endif ?>
    <?php endif ?>
<?php endif ?>


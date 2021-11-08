<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue intermédiaire d'affichage de la liste des articles d'un usager -->

<h2>Mes articles</h2>
<p class="alert alert-<?= $_SESSION["alerte-type"] ?>">
    <?= $_SESSION["alerte"] ?>
    <?php if ($_SESSION["alerte-type"] === "error") : ?>
        Voulez vous <a class="alert-link" href="index.php?commande=articles-form-ajout">ajouter un nouvel article</a> ?
    <?php endif ?>
</p>
<?php if ($nombreResultats > 0) : ?>
    <?php require_once("articles-liste.php") ?>
<?php endif ?>

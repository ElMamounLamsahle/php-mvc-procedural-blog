
<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    // - L'accés directement à la page des recherche n'est pas interdit mais au tant que vue dans la page
    //   index.php
    if (!isset($_commande)) {
        header("Location: ../index.php?commande=articles-recherche");
        die();
    }
?>

<!-- Vue intermédiaire d'affichage des résultats de recherches dans les articles -->

<?php require_once("articles-form-recherche.php"); ?>
<?php if (isset($_SESSION["alerte"])) : ?>
    <p class="alert alert-<?= $_SESSION["alerte-type"] ?>"><?= $_SESSION["alerte"] ?></p>
<?php else : ?>
    <?php if ($nombreResultats > 0) : ?>
        <?php $s = ($nombreResultats > 1) ? "s" : "" ?>
        <p class="alert alert-success"><?= $nombreResultats ?> résultat<?= $s ?> trouvé<?= $s ?> pour vôtre recherche.</p>
        <?php require_once("articles-liste.php"); ?>
    <?php endif ?>
<?php endif ?>
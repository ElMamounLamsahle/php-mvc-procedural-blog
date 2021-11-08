<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue du formulaire de recherche dans les articles -->

<?php if (isset($critereRecherche)) : ?>
    <h2>Recherche dans les articles</h2>
    <form class="grid recherche-form-page" method="POST">
        <input type="text" name="critere-recherche" value="<?= $critereRecherche ?>">
        <div class="validation">
            <input class="btn" type="submit" name="chercher" value="Rechercher">
            <a class="btn" href="<?= get_source(0) ?>">Annuler</a>
            <input type="hidden" name="commande" value="articles-recherche">
            <input type="hidden" name="source" value="<?= $_commande ?>">
        </div>
    </form>
<?php else : ?>
    <form class="grid" method="POST">
        <label class="visually-hidden" for="critere-recherche">Recherche dans les articles</label>
        <input type="text" name="critere-recherche" id="critere-recherche" value="" placeholder="Recherche dans les articles">
        <div>
            <input class="btn" type="submit" name="chercher" value="Rechercher">
            <input type="hidden" name="commande" value="articles-recherche">
            <input type="hidden" name="source" value="<?= $_commande ?>">
        </div>
    </form>
<?php endif ?>
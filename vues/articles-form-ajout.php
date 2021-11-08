<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (strpos($_SERVER["SELF_PHP"], 'are') !== false) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue du formulaire d'ajout d'un article -->

<h2>Nouvel article</h2>
<div class="form">
    <?php if (isset($_SESSION["alerte"])) : ?>
        <form method="POST">
            <div class="container">
                <label for="titre">Titre</label>
                <input required type="text" name="titre" id="titre" value="<?= $titre ?>"/>
                <label for="texte">Contenu</label>
                <textarea required id="texte" name="texte" rows="6" cols="50"><?= $texte ?></textarea>
            </div>
            <div class="validation">
                <input class="btn" type="submit" name="valider" value="Valider"/>
                <a class="btn" href="<?= get_source() ?>">Annuler</a>
                <input type="hidden" name="commande" value="articles-ajout"/>
            </div>
        </form>
        <p class="alert alert-<?= $_SESSION["alerte-type"] ?>"><?= $_SESSION["alerte"] ?></p>
    <?php else : ?>
        <form method="POST">
            <div class="container">
                <label for="titre">Titre</label>
                <input required type="text" name="titre" id="titre" value=""/>
                <label for="texte">Contenu</label>
                <textarea required id="texte" name="texte" rows="6" cols="50"></textarea>
            </div>
            <div class="validation">
                <input class="btn" type="submit" name="valider" value="Valider"/>
                <a class="btn" href="<?= get_source() ?>">Annuler</a>
                <input type="hidden" name="commande" value="articles-ajout"/>
            </div>
        </form>
    <?php endif ?>
</div>
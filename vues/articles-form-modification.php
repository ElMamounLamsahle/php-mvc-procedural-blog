<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue du formulaire de modification d'un article -->

<h2>Modifier un article</h2>
<div class="form">
    <?php if (isset($_SESSION["alerte"])) : ?>
        <form class="form" method="POST">
            <div class="container">
                <label for="titre">Titre</label>
                <input required type="text" name="titre" id="titre" value="<?= $titre ?>"/>
                <label for="texte">Contenu</label>
                <textarea required id="texte" name="texte" rows="6" cols="50"><?= $texte ?></textarea>
            </div>
            <div class="validation">
                <input class="btn" type="submit" name="valider" value="Valider"/>
                <a class="btn" href="<?= get_source() ?>">Annuler</a>
                <input type="hidden" name="commande" value="articles-modification"/>
            </div>
        </form>
        <p class="alert alert-<?= $_SESSION["alerte-type"] ?>"><?= $_SESSION["alerte"] ?></p>
    <?php else : ?>
        <?php $rangee = mysqli_fetch_assoc($donnees["article-a-modifier"]); ?>
        <form class="form" method="POST">
            <div class="container">
                <label for="titre">Titre</label>
                <input required type="text" name="titre" id="titre" value="<?= htmlspecialchars($rangee["titre"], ENT_QUOTES) ?>"/>
                <label for="texte">Contenu</label>
                <textarea required id="texte" name="texte" rows="6" cols="50"><?= htmlspecialchars($rangee["texte"], ENT_QUOTES) ?></textarea>
            </div>
            <div class="validation">
                <input class="btn" type="submit" name="valider" value="Valider"/>
                <a class="btn" href="<?= get_source() ?>">Annuler</a>
                <input type="hidden" name="commande" value="articles-modification"/>
            </div>
        </form>
    <?php endif ?>
</div>
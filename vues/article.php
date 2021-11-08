<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue d'affichage du contenu global d'un article -->

<h2>Article</h2>
<?php while($rangee = mysqli_fetch_assoc($donnees["article"])) : ?>
    <div class="container article">
        <div>
            <h3><?= htmlspecialchars($rangee["titre"], ENT_QUOTES) ?></h3>
            <span class="user"><?= htmlspecialchars($rangee["auteur"], ENT_QUOTES) ?><span>
        </div>
        <?php if (isset($_SESSION["usager"]) && $rangee["usager"] === $_SESSION["usager"]) : ?>
            <div class="validation">
                <a class="btn-small" href="index.php?commande=articles-form-modification&idArticle=<?= $rangee["id"] ?>">Modifier</a>
                <a class="btn-small" href="index.php?commande=articles-suppression&idArticle=<?= $rangee["id"] ?>">Supprimer</a>
            </div>
        <?php endif ?>
        <div class="content">
            <p><?= htmlspecialchars($rangee["texte"], ENT_QUOTES) ?></p>
        </div>
    </div>
<?php endwhile ?>
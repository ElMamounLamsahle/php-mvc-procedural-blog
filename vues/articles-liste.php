<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue d'affichage des la liste des articles -->

<?php while($rangee = mysqli_fetch_assoc($donnees["articles"])) : ?>
    <div class="container article">
        <div>
            <h3><?= htmlspecialchars($rangee["titre"], ENT_QUOTES) ?></h3>
            <?php if ($_commande != "articles-usager") : ?>
                <span class="user"><?= htmlspecialchars($rangee["auteur"], ENT_QUOTES) ?><span>
            <?php endif ?>
        </div>
        <div class="content">
            <?php $contenu = htmlspecialchars($rangee["texte"], ENT_QUOTES) ?>
            <?php if ((mb_strlen($contenu) - 200) > 50) : ?>
                <p><?= mb_substr($contenu, 0, 200) ?>
                    ...<a class="link" href="index.php?commande=article&idArticle=<?= $rangee["id"] ?>">lire la suite</a>
                </p>
            <?php else : ?>
                <p><?= $contenu ?></p>
            <?php endif ?>
        </div>
        <div class="validation">
            <?php if (isset($_SESSION["usager"]) && $rangee["usager"] === $_SESSION["usager"]) : ?>
                <a class="btn-small" href="index.php?commande=articles-form-modification&idArticle=<?= $rangee["id"] ?>">Modifier</a>
                <a class="btn-small" href="index.php?commande=articles-suppression&idArticle=<?= $rangee["id"] ?>">Supprimer</a>
            <?php endif ?>
        </div>
    </div>
<?php endwhile ?>
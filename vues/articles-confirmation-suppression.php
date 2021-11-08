<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

<!-- Vue de confirmation de suppression d'un article -->

<h2>Suppression d'article</h2>
<p class="alert alert-error">Voulez vous vraiment supprimer cet article ?</p>
<?php while($rangee = mysqli_fetch_assoc($donnees["article-a-supprimer"])) : ?>
    <div class="container article">
        <h3><?= htmlspecialchars($rangee["titre"], ENT_QUOTES) ?></h3>
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
            <a class="btn" href="index.php?commande=articles-suppression">Supprimer</a>
            <a class="btn" href="<?= get_source() ?>">Annuler</a>
        </div>
    </div>
<?php endwhile ?>
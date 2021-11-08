<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    // - L'accés directement à la page de connexion n'est pas interdit mais au tant que vue dans la page
    //   index.php
    if (!isset($_commande)) {
        header("Location: ../index.php?commande=login-form");
        die();
    }
?>

<!-- Vue du formulaire de connexion -->

<?php if (isset($_SESSION["usager"])) : ?>
    <?php 
        header("Location: ../index.php");
        die();
    ?>
<?php else : ?>
    <h2 class="login-title">Connexion</h2>
    <div class="form login">
        <form method="POST">
            <div class="container">
                <label for="usager">Nom d'utilisateur</label>
                <input required type="text" name="usager" id="usager" value="<?= (isset($erreurAutantification)) ? $user : "" ?>"/>
                <label for="mot-de-passe">Mot de passe</label>
                <input required type="password" name="mot-de-passe" id="mot-de-passe"/>
            </div>
            <div class="validation">
                <input class="btn" type="submit" name="login" value="Se connecter"/>
                <a class="btn" href="<?= get_source() ?>">Annuler</a>
                <input type="hidden" name="commande" value="login-validation"/>
            </div>
        </form>
        <?php if (isset($_SESSION["alerte"])) : ?>
            <p class="alert alert-<?= $_SESSION["alerte-type"] ?>"><?= $_SESSION["alerte"] ?></p>
        <?php endif ?>
    </div>
<?php endif ?>
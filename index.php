<?php

    // - Démarrage de la session.
    session_start();

    // - Récupération du parmètre commande s'il est défini ou affectation d'une valeur par défaut au cas contraire. 
    if(isset($_REQUEST["commande"])) {
        $_commande = $_REQUEST["commande"];
    }
    else {
        $_commande = "accueil";
    }

    // - Déclaration de la variable titre de la page avec une valeur par défaut qui peut être modifié dans la structure décisionnelle du contrôleur.
    $titrePage = "Blog multi-usagers - Accueil";

    // - Définition d'une variable $_vue ayant valeur par défaut la valeur de la variable $_commande pour ne pas manipuler cette variable aprés.
    // - Car l'inclusion des vues se fait aprés la sortie de la structure décisionnelle du contrôleur, et la vue incluse entre le head et le footer
    //   peux changer selon les valeurs des autres parmètres et les résultats des fonctions du modèle.
    $_vue = $_commande;

    // - Réinitialisation des deux sessions utilisées lors de la modification et la suppression des articles si l'usager a quitté ces processus
    //   avant leurs fin en choisissant une autre commande depuis le menu ou via l'url.
    // - Ces deux sessions permettent de bien sécuriser ces deux opérations en plus des contôles habituels.
    if (isset($_SESSION["usager"])) {
        if (isset($_SESSION["id-article-a-modifier"]) && $_commande != "articles-form-modification" && $_commande != "articles-modification") {
            unset($_SESSION["id-article-a-modifier"]);
        }
        if (isset($_SESSION["id-article-a-supprimer"]) && $_commande != "articles-suppression") {
            unset($_SESSION["id-article-a-supprimer"]);
        }
    }

    // - J'ai laissé ces fonction ici pour que vous faciliter la lecture du code sans faire des aller-retour vers une page des fonctions

    /**
     * - Fonction de redériction pour éviter la redendance.
     * - La redériction dans les vues est codée en dûr car l'usager peu entrer l'adresse de la page dans la bare de navigation
     *   sans passer pae "index.php", mais si la variable $_commande n'est pas initialisée je le redirige vers "index.php".
     * @param  string [$path] - L'url vers le quel l'usager sera redirigé, s'il n'est pas défini il sera redirigé vers "index.php".
    */
    function rediriger($path = "index.php") {
        header("Location: ". $path);
        die();
    }

    /**
     * - Fonction de centralisation du nettoyage des paramètres pour éviter la redendance
     * @param  string $parametre - Le paramètre attendu par la requête GET ou POST
    */
    function get_REQUEST($parametre) {
        $parametre = $_REQUEST[$parametre] ?? "";
        return trim($parametre);
    }

    /**
     * - Fonction utilisée pour la gestion du bouton "Annuler", en utilisant une session "source".
     * - Cette fonction crée ou réinitialise le tableau destiné à stocké des liens incluant les pramètres vers quelques pages que l'usager a visité.
     * - Elle est appelée depuis des pages qui ne contiennent pas un bouton "Annuler", mais sur lesquelles il y a
     *   des liens qui redirigent vers des pages qui contiennent le boutons "Annuler".
     * @param  string [$parametres] - une chaîne qui correspond à la partie aprés "index.php?commande=".
     *                                S'il n'est pas défini seule la commande en cours est enrgistré dans le tableau.
    */
    function set_source($parametres = "") {
        global $_commande;
        $_SESSION["source"] = array();
        if ($parametres != "") $_SESSION["source"][] = $_commande ."&". $parametres;
        else $_SESSION["source"][] = $_commande;
    }

    /**
     * - Fonction utilisée pour la gestion du bouton "Annuler", pour en savoir plus
     *   veuillez consulter la documentation de la fonction "set-source()" ci-dessus.
     * - Cette fonction ajoute une nouvelle valeur à la fin du tableau créé dans la fonction précédente "set-source()".
     * - Elle est appelée depuis des pages qui contiennent un bouton "Annuler", et sur lesquelles il y a des liens
     *   qui redirigent vers des pages qui contiennent elles aussi le boutons "Annuler".
     * @param  string [$parametres] - une chaîne qui correspond à la partie aprés "index.php?commande=".
     *                                S'il n'est pas défini seule la commande en cours est ajoutée au tableau.
    */
    function add_source($parametres = "") {
        global $_commande;
        if ($parametres != "") $_SESSION["source"][] = $_commande ."&". $parametres;
        else $_SESSION["source"][] = $_commande;
    }

    /**
     * - Fonction utilisée pour la gestion du bouton "Annuler", pour en savoir plus
     *   veuillez consulter la documentation des fonctions "set-source()" et "add_source()" ci-dessus.
     * - Cette fonction récupère du tableau alimenté par les fonctions "set_source()" et "add_source()"
     *   la valeur correspondante de l'index passé en paramètre, ou la dérnière valeur de ce tableau.
     * - Elle est appelée depuis les vues pour générer l'attribut href du bouton (a) "Annuler".
     * - La première ligne de cette fonction teste l'existance de la session "source" et vérifie si le parmètre est du type entier
     *   si le teste échoue, la fonction retourne "index.php".
     * @param  string [$index] - l'index de la valeur à retourner du tableau, s'il n'est pas défini la dérnière valeur du tableau est retournée.
     *                           La valeur par défaut est défini comme string pour facilité le test mais si le paramètre est défini il doit être un entier.
    */
    function get_source($index = "") {
        if (!isset($_SESSION["source"]) || ($index != "" && !is_integer($index))) return "index.php";
        if ($index !== "") {
            return "index.php?commande=".$_SESSION["source"][$index];
        }
        else {
            return "index.php?commande=". end($_SESSION["source"]);
        }
    }

    // - Inclusion du modèle.
    require_once("modele.php");

    // - J'ai choisi d'enregistrer les messages d'erreurs et leurs types (class css) dans des sessions au lieu des variables pour pouvoir
    //   les afficher même aprés une redériction.
    //   Les sessions des messages d'erreurs sont détruites aprés l'inclusion des vus tout en bas de ce script, s'il y a une redériction elle ne sont pas
    //   détruites ce qui me permet de les afficher en tous les cas sans faire des tests pour savoir s'il s'agit d'une redériction ou pas.

    // - Structure décisionnelle du contrôleur
    // - Si le nom de la vue à inclure est égal à la commande je ne modifie pas la variable $_vue 
    switch($_commande) {
        case "accueil" :
            // - Appel de la fonction set_source() et initialisation des variables necessaires au affichage de la vue "accueil",
            //   car sur la page générée il y aura un lien qui redirige vers la page des recherche qui contient un bouton "Annuler".
            // - Le titre de la page est déja initialisé
            set_source();
            $barRechercheHeader = true;
            $donnees["articles"] = obtenir_articles();
            break;
        case "login-form" :
            // - Pas la peine ici d'appeler la fonction set_source, seulement mise à jour de du titre de la page
            $titrePage = "Blog - Se connecter";
            break;
        case "login-validation" :
            // - Cette commande est appelée depuis la page de connexion
            $user = get_REQUEST("usager");
            $password  = get_REQUEST("mot-de-passe");
            if ($user !== "" && $password !== "") {
                $login = login($user, $password);
                if ($login) {
                    $_SESSION["usager"] = $user;
                    $_SESSION["nom-usager"] = obtenir_nom_usager($user);
                    // - Combinaison des fonctions de redériction de l'usager aprés une authentification juste.
                    // - Si l'utilisateur a accédé au formulaire de connexion à partir d'une page qui appelle la fonction set_source() ou add_source()
                    //   il est redirigé vers cette page, si non il est redirigé vers la page "index.php" sans parmètres.
                    rediriger(get_source());
                }
                else $_SESSION["alerte"] = "Mauvaise combinaison nom d'utilisateur / mot de passe.";
            }
            else {
                $_SESSION["alerte"] = "Tous les champs sont requis.";
            }
            // - Si l'authentification a échoué je modifie la valeur de la variable $_vue
            //   pour réafficher le formulaire de connexion avec un message de l'erreur survenue et je modifie le titre de la page
            $_vue = "login-form";
            $titrePage = "Blog - Erreur authentification";
            $_SESSION["alerte-type"] = "error";
            break;
        case "logout" :
            // - Déconnexion et redériction vers la page "index.php" sans parmètres.
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            rediriger();
            break;
        case "article" :
            // - Vue d'ffichage d'un article complet aprés le clique sur le lien "lire la suite"
            $idArticle = get_REQUEST("idArticle", true);
            if (is_numeric($idArticle)) {
                $donnees["article"] = obtenir_article_par_id($idArticle);
                if ($donnees["article"] === false) {
                    // - Si l'usager a manipuler l'id de l'article il est redérigé vers la page "index.php" avec un message d'erreur.
                    //   car la requête n'est pas censé d'échouer.
                    $_SESSION["alerte"] = "L'article que vous désirez consulter n'existe pas.";
                    $_SESSION["alerte-type"] = "error";
                    // - Si la session "alerte" est définie seule le message d'erreur est affiché
                    rediriger();
                }
                else {
                    // - Appel de la fonction set_source() et initialisation des variables necessaires au affichage de la vue "article",
                    //   car sur cette vue il y aura un lien qui redirige vers la page des recherche qui contient un bouton "Annuler".
                    set_source("idArticle=$idArticle");
                    $titrePage = "Blog - Lecture d'article";
                    $barRechercheHeader = true;
                }
            }
            // - Si l'usager a manipuler l'id de l'article il est redérigé vers la page "index.php" sans message d'erreur.
            else rediriger();
            break;
        case "articles-usager" :
            // - Vue d'affichage de la liste des article d'un usager authentifié
            if (isset($_SESSION["usager"])) {
                // - Si l'usager est authentifié la fonction set_source() est appelée
                set_source();
                // - initialisation des variables nécessaires à l'affichage de la vue "articles-usagers"
                $titrePage = "Blog - Mes articles";
                $barRechercheHeader = true;
                $donnees["articles"] = obtenir_articles_par_usager($_SESSION["usager"]);
                $nombreResultats = mysqli_num_rows($donnees["articles"]);
                if ($nombreResultats === 0) {
                    $_SESSION["alerte"] = "Vous n'avez aucun article en ligne.";
                    $_SESSION["alerte-type"] = "error";
                }
                else {
                    $s = ($nombreResultats > 1) ? "s" : "";
                    $_SESSION["alerte"] = "Vous avez $nombreResultats article$s en ligne."  ;
                    $_SESSION["alerte-type"] = "info";
                }
            }
            // - Si un usager non authenifié a saisi dans la barre de navigaion "index.php?commande=articles-usager"
            //   il est redérigé vers la page "index.php" sans message d'erreur.
            else rediriger();
            break;
        case "articles-recherche" :
            // - Vue des recherches dans les articles
            // - Initialisation des variables necessaires au affichage de la vue
            $titrePage = "Blog - Recherche d'articles";
            $critereRecherche = get_REQUEST("critere-recherche");
            $source = get_REQUEST("source");
            $donnees["articles"] = false;
            // - Si l'usager a cliqué sur le bouton de recherche sans saisir un critère de recherche
            if ($critereRecherche == "") {
                // - Si le clique est fait sur le bouton du petit formulaire en haut de la page et pas celui de la page des recherches
                //   une redériction est faite vers $_SERVER["REQUEST_URI"] pour qu'il reste sur la même page comme si rien n'est passé 
                if ($source != "articles-recherche") rediriger($_SERVER["REQUEST_URI"]);
                // - Si non, c'est à dire que le clique est fait sur le bouton de la page de recherche
                //   la variable $nombreResultats prend 0 pour pouvoir un test sur la valeur de la variable $nombreResultats 
                //   dans la vue "articles-recherche" pour savoir si on doit inclure la vue des liste des aricles ou pas
                else $nombreResultats = 0;
            }
            else {
                // - Si un critère de recherche est saisi la variable $donnees["articles"] prend le résultat de la fonction chercher_articles()
                $donnees["articles"] = chercher_articles($critereRecherche);
                // - Si le résultat vaut null c'est à dire qu'il y a une tentative d'injoncion SQL et la préparation de la requête a échoué
                //   alors l'usager est rediriger vers "index.php" avec un message d'erreur pour qu'il sache que c'est inutile.
                if ($donnees["articles"] === null) {
                    $_SESSION["alerte"] = "La recherche a échoué.";
                    $_SESSION["alerte-type"] = "error";
                    rediriger();
                }
                else {
                    // - Si non la variable $nombreResultats prend le nombre des lignes du résultat
                    $nombreResultats = mysqli_num_rows($donnees["articles"]);
                    if ($nombreResultats == 0) {
                        $_SESSION["alerte"] = "Aucun résultat trouvé pour vôtre recherche.";
                        $_SESSION["alerte-type"] = "error";
                    }
                }
            }
            // - Appel de la fonction add_source() car sur la vue "articles-recherche" a un bouton "Annuler" et des liens qui redirigent 
            //   vers des vus qui ont aussi un bouton "Annuler" comme la vue de connexion.
            add_source("critere-recherche=$critereRecherche&source=$source");
            break;
        case "articles-form-ajout" :
            // - Vue de formulaire d'ajout
            // - Si l'usager est authentifié, le titre de la page est mis à jour
            if (isset($_SESSION["usager"])) {
                $titrePage = "Blog - Nouvel article";
            }
            // - Si un usager non authenifié a saisi dans la barre de navigaion "index.php?commande=articles-form-ajout"
            //   il est redérigé vers la page "index.php" sans message d'erreur.
            else rediriger();
            break;
        case "articles-ajout" :
            // - Validation de l'ajout d'article
            // - Si l'usager est authentifié
            if (isset($_SESSION["usager"])) {
                $titre = get_REQUEST("titre");
                $texte = get_REQUEST("texte");
                if ($titre != "" && $texte != ""){
                    // - Si les paramètres requis sont récupérés
                    // - L'ajout de l'article est fait avec une valeur de retour
                    $test = ajouter_article($_SESSION["usager"], $titre, $texte);
                    if ($test) {
                        // - Si l'ajout a réussi l'usager est redériger vers "index.php"
                        //   pour pouvoir visualiser que son article a été bien ajouté
                        //   car le redériction vers la page précédente n'est pas optimal dans ce cas
                        rediriger();
                    }
                    else if (is_null($test)) {
                        // - Si $test vaut null c'est à dire qu'il y a une tentative d'injoncion SQL et la préparation de la requête a échoué
                        //   alors l'usager est rediriger vers "index.php" avec un message d'erreur pour qu'il sache que c'est inutile.
                        $_SESSION["alerte"] = "L'ajout de l'article a échoué.";
                        $_SESSION["alerte-type"] = "error";
                        rediriger();
                    }
                    else {
                        // - Si l'ajout a échoué pour une raison imprévue la valeur de la variable $_vue est modifié pour laisser l'usager
                        //   sur la page de l'ajout d'article avec un message d'erreur
                        $_vue = "articles-form-ajout";
                        $titrePage = "Blog - erreur ajout article";
                        $_SESSION["alerte"] = "L'ajout de l'article a échoué.";
                        $_SESSION["alerte-type"] = "error";
                    }
                }
                else {
                    // - Si un des paramètres n'est pat reçu ou tous les deux la valeur de la variable $_vue est modifié pour laisser l'usager
                    //   sur la page de l'ajout d'article avec un message d'erreur
                    $_vue = "articles-form-ajout";
                    $titrePage = "Blog - erreur ajout article";
                    $_SESSION["alerte"] = "Tous les champs sont requis.";
                    $_SESSION["alerte-type"] = "error";
                }
            }
            else {
                // - Si un usager non authenifié a saisi dans la barre de navigaion "index.php?commande=articles-ajout"
                //   il est redérigé vers la page "index.php" sans message d'erreur.
                rediriger();
            }
            break;
        case "articles-form-modification" :
            // - Vue de modification d'article
            // - Si l'usager est authentifié
            if (isset($_SESSION["usager"])) {
                $idArticle = get_REQUEST("idArticle");
                if (is_numeric($idArticle)) {
                    // - Si le paramètre idArticle est numerique l'id de l'auteur de l'article est vérifié
                    $auteur = obtenir_id_auteur_article($idArticle);
                    if ($auteur === $_SESSION["usager"]) {
                        // - Si l'usager est l'auteur de l'aricle
                        // - initialisation des variables nécessaires à l'affichage de la vue "articles-form-modification"
                        $donnees["article-a-modifier"] = obtenir_article_par_id($idArticle);
                        $titrePage = "Blog - Modifier un article";
                        // - Stockage de l'id de l'aricle à modifier dans une session pour pouvoir bien sécuriser
                        //   l'opération de la mise à jour et ne pas permettre à l'usager de manipuler l'id de l'article à modifier
                        //   et pour ne pas modifier un article par un autre
                        $_SESSION["id-article-a-modifier"] = $idArticle;
                    }
                    else {
                        // - Si l'usager n'est pas l'auteur de l'aricle
                        if ($auteur) {
                            // - Si l'article existe alors un message indiquant à l'usager qu'il n'a pas le droit de modifier cet aricle
                            //   est socké dans la session "alerte".
                            $_SESSION["alerte"] = "Vous n'avez pas le droit de modifier l'article sélectionné.";
                            $_SESSION["alerte-type"] = "error";
                        }
                        else {
                            // - Si l'usager a modifier l'id de l'article manuellement avec un autre inéxistant
                            //   un message indiquant à l'usager que cet aricle n'éxiste pas
                            //   est socké dans la session "alerte".
                            $_SESSION["alerte"] = "L'article que vous désirez modifier n'existe pas.";
                            $_SESSION["alerte-type"] = "info";
                        }
                        // - Et l'usager et redirigé vers la page "index.php" avec un message d'erreur.
                        rediriger();
                    }
                }
                else {
                    // - Si le parmètre idArticle n'est pas numérique
                    // - l'usager et redirigé vers la page "index.php" avec un message d'erreur.
                    $_SESSION["alerte"] = "La séléction de l'article a échoué.";
                    $_SESSION["alerte-type"] = "error";
                    rediriger();
                }
            }
            else {
                // - Si un usager non authenifié a saisi dans la barre de navigaion "index.php?commande=articles-form-modification&idArticle=56" par exemple
                //   il est redérigé vers la page "index.php" sans message d'erreur.
                rediriger();
            }
            break;
        case "articles-modification" :
            // - Validation de la modification d'article
            // - Si l'usager est authentifié
            if (isset($_SESSION["usager"])) {
                $titre = get_REQUEST("titre");
                $texte = get_REQUEST("texte");
                if ($titre != "" && $texte != ""){
                    // - Si le titre et le texte sont fournis
                    // - La requête de la mise à jour de l'article est appelée avec en parmètre :
                    //   - La session usager malgré que c'est déja vérifié dans cette commande et la commande précédente
                    //     pour en cas ou l'usager à réussi à dépasser toutes les régles de sécurité il ne pourra en fin de compte
                    //     modifier qu'un article dont il est l'auteur.
                    //   - L'id de l'article déja stocké dans la session "id-article-a-modifier".
                    //   - Et les nouveaux titre et texte de l'article.
                    $test = modifier_article($_SESSION["usager"], $_SESSION["id-article-a-modifier"], $titre, $texte);
                    if ($test) {
                        // - Si la mise à jour a réussi
                        // - La session "id-article-a-modifier" est détruite et l'usager est redirigé vers "index.php"
                        unset($_SESSION["id-article-a-modifier"]);
                        rediriger();
                    }
                    else if (is_null($test)) {
                        // - Si $test vaut null c'est à dire qu'il y a une tentative d'injoncion SQL et la préparation de la requête a échoué
                        //   alors l'usager est rediriger vers "articles-form-modification" avec un message d'erreur pour qu'il sache que c'est inutile.
                        $_vue = "articles-form-modification";
                        $_SESSION["alerte"] = "La modification de l'article a échoué.";
                        $_SESSION["alerte-type"] = "error";
                    }
                    else {
                        // - Si $test vaut false c'est à dire que l'usager n'a apporté aucune modification sur l'article
                        //   alors l'usager est rediriger vers "articles-form-modification" avec un message d'erreur pour qu'il prend la décision adéquate
                        //   de faire des modification ou annuler l'opération.
                        $_vue = "articles-form-modification";
                        $_SESSION["alerte"] = "Vous n'avez rien modifié dans l'article.";
                        $_SESSION["alerte-type"] = "error";
                    }
                }
                else {
                    // - Si un des paramètres n'est pat reçu ou tous les deux la valeur de la variable $_vue est modifié pour laisser l'usager
                    //   sur la page de la modification d'article avec un message d'erreur
                    $_vue = "articles-form-modification";
                    $_SESSION["alerte"] = "Tous les champs sont requis.";
                    $_SESSION["alerte-type"] = "error";
                }
                $titrePage = "Blog - erreur modification article";
            }
            else {
                // - Si un usager non authenifié a saisi dans la barre de navigaion "index.php?commande=articles-modification"
                //   il est redérigé vers la page "index.php" sans message d'erreur.
                rediriger();
            }
            break;
        case "articles-suppression" :
            // - Traitement de la suppression d'un article
            // - Si l'usager est authentifié
            if (isset($_SESSION["usager"])) {
                // Si la session "id-article-a-supprimer" est déja définie
                if (isset($_SESSION["id-article-a-supprimer"])) {
                    // - La requête de la suppression de l'article est appelée avec en parmètre :
                    //   - La session usager malgré que c'est déja vérifié, 
                    //     pour en cas ou l'usager à réussi à dépasser toutes les régles de sécurité il ne pourra en fin de compte
                    //     supprimer qu'un article dont il est l'auteur.
                    //   - L'id de l'article déja stocké dans la session "id-article-a-supprimer".
                    $test = supprimer_article($_SESSION["usager"], $_SESSION["id-article-a-supprimer"]);
                    // - La session "id-article-a-supprimer" est détruite et la valeur retournée par $test est vérifiée
                    unset($_SESSION["id-article-a-supprimer"]);
                    if ($test) {
                        // - Si la suppression est bien faite l'usager est redirigé vers la page sur la quelle il a cliqué sur "Supprimer".
                        rediriger(get_source());
                    }
                    else {
                        // - Si la suppression a échoué l'usager est redirigé vers la page "index.html" avec un message d'erreur.
                        $_SESSION["alerte"] = "La suppression de l'article a échoué.";
                        $_SESSION["alerte-type"] = "error";
                        rediriger();
                    }
                }
                else {
                    $idArticle = get_REQUEST("idArticle");
                    if (is_numeric($idArticle)){
                        // - Si le paramètre idArticle est numerique l'id de l'auteur de l'article est vérifié
                        $auteur = obtenir_id_auteur_article($idArticle);
                        if ($auteur === $_SESSION["usager"]) {
                            // - Si l'usager est l'auteur de l'aricle
                            // - initialisation des variables nécessaires à l'affichage de la vue "articles-confirmation-suppression"
                            $_vue = "articles-confirmation-suppression";
                            $titrePage = "Blog - Confirmation de la suppression d'un article";
                            $donnees["article-a-supprimer"] = obtenir_article_par_id($idArticle);
                            // - Stockage de l'id de l'aricle à supprimer dans une session pour pouvoir bien sécuriser
                            //   l'opération de la suppression et ne pas permettre à l'usager de manipuler l'id de l'article à supprimer
                            //   et pour ne pas supprimer un article au lieu  d'un autre.
                            $_SESSION["id-article-a-supprimer"] = $idArticle;
                        }
                        else {
                            // - Si l'usager n'est pas l'auteur de l'aricle
                            if ($auteur) {
                                // - Si l'article existe alors un message indiquant à l'usager qu'il n'a pas le droit de supprimer cet aricle
                                //   est socké dans la session "alerte".
                                $_SESSION["alerte"] = "Vous n'avez pas le droit de supprimer l'article sélecionné.";
                                $_SESSION["alerte-type"] = "error";
                            }
                            else {
                                // - Si l'usager a modifier l'id de l'article manuellement avec un autre inéxistant
                                //   un message indiquant à l'usager que cet aricle n'éxiste pas
                                //   est socké dans la session "alerte".
                                $_SESSION["alerte"] = "L'article que vous désirez supprimer n'existe pas.";
                                $_SESSION["alerte-type"] = "error";
                            }
                            // - Et l'usager et redirigé vers la page "index.php" avec un message d'erreur.
                            rediriger();
                        }
                    }
                }
            }
            else {
                // - Si un usager non authenifié a saisi dans la barre de navigaion "index.php?commande=articles-suppression&idArticle=56" par exemple
                //   il est redérigé vers la page "index.php" sans message d'erreur.
                rediriger();
            }
            break;
        default:
            // - Action non traitée, commande invalide -- redirection
            rediriger();
    }

    // - Inclusion des vues
    require_once("vues/head.php");
    require_once("vues/". $_vue .".php");
    require_once("vues/footer.php");

    // - Détruire les session alerte et alerte-type (class css)
    unset($_SESSION["alerte"],$_SESSION["alerte-type"]);

    // - Fermeture de la connexion s'elle n'est pas déja fermée
    fermer_connexion();

?>
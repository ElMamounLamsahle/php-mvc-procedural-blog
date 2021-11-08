<?php

    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: index.php");
        die();
    }

    // - Définition des constantes de la connexion
    define("SERVER", "localhost");
    define("USERNAME", "root");
    define("PASSWORD", "root");
    define("DB_NAME", "blog");

    /**
     * Fonction de connexion à la base de données
     * @return object  $connexion - l'objet $connexion
    */
    function connexion() {
        $connexion = mysqli_connect(SERVER, USERNAME, PASSWORD, DB_NAME);
        if(!$connexion) {
            trigger_error("Erreur de connexion : " . mysqli_connect_error());
            die();
        }
        mysqli_query($connexion, "SET NAMES 'utf8'");
        return $connexion;
    }

    // Stockage de l'objet connexion dans la variable $connexion
    $connexion = connexion();

    /**
     * Fonction qui ferme la connexion à la base de données
    */
    function fermer_connexion() {
        global $connexion;
        mysqli_close($connexion);
    }

    /**
     * Fonction d'authentification
     * @param string $user                  - Nom d'utilisateur
     * @param string $password              - Mot de passe
     * @return boolean|null true|false|null - true  : Si la combinaison nom d'utilisateur / mot de passe est correcte.
     *                                      - false : Si la combinaison n'est pas correcte
     *                                      - null  : Si la préparation de la requête a échoué pour une tenative d'injenction SQL ou une autre raison,
     *                                                pour pouvoir traité cette exeption sans afficher un message technique aux usagers 
    */
    function login($user, $password) {
        global $connexion;
        $requete = "SELECT password
                    FROM usagers
                    WHERE username = ?";
        if($requetePreparee = mysqli_prepare($connexion, $requete)) {
            mysqli_stmt_bind_param($requetePreparee, 's', $user);
            mysqli_stmt_execute($requetePreparee);
            $resultats = mysqli_stmt_get_result($requetePreparee);
            if(mysqli_num_rows($resultats) > 0) {
                $rangee = mysqli_fetch_assoc($resultats);
                $passwordEncrypte = $rangee["password"];
                if(password_verify($password, $passwordEncrypte)) {
                    return true;
                }
                else return false;
            }
            else return false;
        }
        else return null;
    }

    /**
     * Fonction pour obtenir le nom complet d'un usager à partir de son nom d'utilisateur
     * @param string  $user          - Nom d'utilisateur
     * @return string $rangee["nom"] - Valeur unique du tableau de résultat sous forme de concatenation du prénom et nom de l'usager
    */
    function obtenir_nom_usager($user) {
        global $connexion;
        $requete = "SELECT CONCAT(prenom, ' ', nom) AS nom
                    FROM usagers
                    WHERE username = '$user'";
        $resultat = mysqli_query($connexion, $requete);
        $rangee = mysqli_fetch_assoc($resultat);
        return $rangee["nom"];
    }

    /**
     * Fonction pour obtenir la liste de tous les articles de la base de données
     * @return array|boolean $resultat|false - Tableau du résultat de la requête ou false si la requête n'a retourné aucune ligne
    */
    function obtenir_articles() {
        global $connexion;
        $requete = "SELECT id , titre, texte, idAuteur As usager, CONCAT(prenom, ' ', nom) As auteur
                    FROM articles JOIN usagers ON articles.idAuteur = usagers.username
                    ORDER BY id DESC";
        $resultat = mysqli_query($connexion, $requete);
        if (mysqli_num_rows($resultat) > 0) return $resultat;
        else return false;
    }

    /**
     * Fonction pour obtenir le nom d'utilisateur d'un article
     * @param  integer $idArticle - L'id de l'article
     * @return string|boolean $rangee["idAuteur"]|false - Nom d'utilisateur de l'auteur ou false si la requête n'a retourné aucune ligne
    */
    function obtenir_id_auteur_article($idArticle) {
        global $connexion;
        $requete = "SELECT idAuteur
                    FROM articles
                    WHERE id = $idArticle";
        $resultat = mysqli_query($connexion, $requete);
        if(mysqli_affected_rows($connexion) > 0) {
            $rangee = mysqli_fetch_assoc($resultat);
            return $rangee["idAuteur"];
        }
        else return false;
    }

    /**
     * Fonction pour obtenir un article par son id
     * @param  integer       $idArticle      - L'id de l'article
     * @return array|boolean $resultat|false - Tableau du résultat de la requête ou false si la requête n'a retourné aucune ligne
    */
    function obtenir_article_par_id($idArticle) {
        global $connexion;
        $requete = "SELECT id , titre, texte, idAuteur As usager, CONCAT(prenom, ' ', nom) As auteur
                    FROM articles JOIN usagers ON articles.idAuteur = usagers.username
                    WHERE id = $idArticle";
        $resultat = mysqli_query($connexion, $requete);
        if(mysqli_affected_rows($connexion) > 0) return $resultat;
        else return false;
    }

    /**
     * Fonction pour obtenir tous les articles rédigés par un usager
     * @param  string $usager   - Nom d'utilisateur
     * @return array  $resultat - Tableau du résultat de la requête
    */
    function obtenir_articles_par_usager($usager) {
        global $connexion;
        $requete = "SELECT id , titre, texte, idAuteur As usager, CONCAT(prenom, ' ', nom) As auteur
                    FROM articles JOIN usagers ON articles.idAuteur = usagers.username
                    WHERE idAuteur = '$usager'";
        $resultat = mysqli_query($connexion, $requete);
        return $resultat;
    }

    /**
     * Fonction de recherche d'un article par un critère contenu dans son titre ou son texte
     * @param  string $critere             - critère de recherche : une lettre, un mot une phrase à rechercher
     * @return array|null  $resultats|null - Tableau du résultat de la requête ou null si la préparation de la requête a échoué 
     *                                       pour une tenative d'injenction SQL ou une autre raison pour pouvoir traité cette exeption
     *                                       sans afficher un message technique aux usagers 
    */
    function chercher_articles($critere) {
        global $connexion;
        $critere = "%".$critere."%";
        $requete = "SELECT id , titre, texte, idAuteur As usager, CONCAT(prenom, ' ', nom) As auteur
                    FROM articles JOIN usagers ON articles.idAuteur = usagers.username 
                    WHERE titre LIKE ? OR texte LIKE ? 
                    ORDER BY id DESC";
        $reqPrep = mysqli_prepare($connexion, $requete);
        if($reqPrep) {
            mysqli_stmt_bind_param($reqPrep, "ss", $critere, $critere);
            mysqli_stmt_execute($reqPrep);
            $resultat = mysqli_stmt_get_result($reqPrep);
            return $resultat;
        }
        else return null;
    }

    /**
     * Fonction d'ajout d'un nouvel article
     * @param  string $usager               - Nom d'utilisateur
     * @param  string $titre                - Titre de l'article
     * @param  string $texte                - Contenu de l'article
     * @return boolean|null true|false|null - true  : Si l'ajout a été bien fait
     *                                      - false : Si l'ajout n'a pas fonctionné
     *                                      - null  : Si la préparation de la requête a échoué pour une tenative d'injenction SQL ou une autre raison,
     *                                                pour pouvoir traité cette exeption sans afficher un message technique aux usagers 
    */
    function ajouter_article($usager, $titre, $texte) {
        global $connexion;
        $requete = "INSERT INTO articles(idAuteur, titre, texte)
                    VALUES (?, ?, ?)";
        if($requetePreparee = mysqli_prepare($connexion, $requete)) {
            mysqli_stmt_bind_param($requetePreparee, 'sss', $usager, $titre, $texte);
            mysqli_stmt_execute($requetePreparee);
            if(mysqli_affected_rows($connexion) > 0) return true;
            else return false;
        }
        else return null;
    }

    /**
     * Fonction de mise à jour un article
     * @param  string  $usager              - Nom d'utilisateur : La raison d'ajout de ce parmètre est expliquée dans le fichier index.php
     * @param  integer $idArticle           - L'id de l'article
     * @param  string  $titre               - Titre de l'article
     * @param  string  $texte               - Contenu de l'article
     * @return boolean|null true|false|null - true  : Si la mise à jour a été bien faite
     *                                      - false : Si la mise à jour n'a pas fonctionné
     *                                      - null  : Si la préparation de la requête a échoué pour une tenative d'injenction SQL ou une autre raison,
     *                                                pour pouvoir traité cette exeption sans afficher un message technique aux usagers 
    */
    function modifier_article($usager, $idArticle, $titre, $texte) {
        global $connexion;
        $requete = "UPDATE articles
                    SET titre = ?, texte = ?
                    WHERE idAuteur = ? AND id = ?";
        if($requetePreparee = mysqli_prepare($connexion, $requete)) {
            mysqli_stmt_bind_param($requetePreparee, 'sssi', $titre, $texte, $usager, $idArticle);
            mysqli_stmt_execute($requetePreparee);
            if(mysqli_affected_rows($connexion) > 0) return true;
            else return false;
        }
        else return null;
    }

    /**
     * Fonction de suppression un article
     * @param  string  $usager      - Nom d'utilisateur : La raison d'ajout de ce parmètre est expliquée dans le fichier index.php
     * @param  integer $idArticle   - L'id de l'article
     * @return boolean true|false   - true  : Si la suppression a été bien faite
     *                              - false : Si la suppression n'a pas fonctionné
    */
    function supprimer_article($usager, $idArticle) {
        global $connexion;
        $requete = "DELETE FROM articles
                    WHERE idAuteur = '$usager' AND id = $idArticle";
        $resultat = mysqli_query($connexion, $requete);
        if(mysqli_affected_rows($connexion) > 0) return true;
        else return false;
    }
?>
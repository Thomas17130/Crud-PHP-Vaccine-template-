<?php
/**
 * LoginHandler.php
 * - Recuperer les functions du connect.php + session_start
 * - Tester si nous faisons un submit du formulaire (test de $_POST)
 * - Si ce n'est pas le cas : alors rien.
 * - Si c'est le cas 
 *  - Vérfication des que le user existe en BDD en utilisant l'email 
 * pour le retrouver (car unique)
 *  - que le password est bien celui de notre BDD (password_verify)
 */
require_once 'connect.php';

/**
 * Si $_POST est > 0 alors nous avons envoyé des éléments.
 * Si nous n'avons pas renseigné de champs alors nous aurons une valeur vide.
 * ex $_POST['nom'] = '' chaine de caractère à 0
 * */
if(isset($_POST["login"])){
    
    /**
     * Définir notre req en SQL
     * Afin de sécuriser notre req, nous ajoutons le param :mail
     * Cela permet de sécuriser notre req afin d'éviter l'injection de données
     * Ces informations doivent être les même que la BDD
     * Dans le SELECT c'est ce que je veux recupérer : mail et password
     * FROM depuis quelle table : users
     * WHERE la condition : il faut que le mail dans la table égal le mail dans
     * le formulaire
     * */
    $sql = "SELECT id, firstname, password FROM users WHERE mail = :post_mail";
    
    $stm = executeStatement(
        $sql,
        [
            'post_mail' => $_POST['mail']
        ]
    );

    /**
     * Récupérer l'utilisateur qui correspond au mail du formulaire
     */
    $user = $stm->fetch(PDO::FETCH_ASSOC);
    
    /** 
     * Si le retour du fetch est "false" alors l'utilisateur n'existe pas 
     * dans notre BDD
     */
    if($user != false){        
        /**
         * Il ne reste qu'à tester le password du formulaire et le password en BDD
         * */
        $isVerified = password_verify($_POST['password'], $user['password']);
        
        if($isVerified == true){
            /**
             * Nous stockons en session les informations du user
             * $_SESSION est un array on peut définir nos propres clés
             */
            $_SESSION['user']['id']         = $user['id'];
            $_SESSION['user']['firstname']  = $user['firstname'];
            
            /**
             * Redirection vers la page de modification des infos du user
             */
            header('Location: modified.html?iduser='.$user['id']);
            exit();
        }
    }
        
}
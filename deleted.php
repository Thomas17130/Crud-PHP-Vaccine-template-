<?php
/**
 * recuperer id de user à supprimer
 * se connecter à la BDD + session (connect.php)
 * verifier que le user connecté puisse le supprimer
 *      -il faut que ce soit son compte
 * supprimer ce compte
 * logout du user
 * ajouter un message de validation en seesion
 * redirection vers index (qui affichera ce message)
 */
require_once('connect.php');

/**verification d'avoir un id user dans l'url */
if(isset($_GET['iduser'])){

}else{

    
}
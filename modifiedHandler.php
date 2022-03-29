<?php
require_once 'connect.php';

//Initialisation de cette variable pour les cas où on ne valide pas le form
$formErrors = [];
/**
 * Si le count POST est > 0 alors nous avons fait un submit
 */
if (isset($_GET['iduser'])) {

    //tester si l'id user du GET == celui de la session
    if(!isset($_SESSION['user']['id']) || $_GET['iduser'] != $_SESSION['user']['id']){
        header('Location: index.html');
        exit();
    }

    //recupération des infos utilisateurs
    $currUser = getUser($_GET['iduser']);

    if($currUser === false){
        header('Location: index.html');
        exit();
    }

    if (count($_POST) > 0) {
        //Récupération du test du formulaire
        $formErrors = validationForm();

        //Si notre form est valide alors on ajoute l'utilisateur
        if ($formErrors['isFormValid'] == true) {
            updateUser();
        }
    }
} else {

    $_SESSION['flashMsg'] = 'Vous ne pouvez pas ouvrir cette page.';

    header('Location: index.html');
    exit();
}

function getUser($idUser)
{
    //Définir notre req
    $sql = "SELECT firstname, lastname, mail, birth, isInfected, isPublic FROM users WHERE id = :param_idUser";

    //Préparer notre req
    $gg = executeStatement($sql, [
        'param_idUser' => $idUser,
    ]);

    return $gg->fetch(PDO::FETCH_ASSOC);
}

/**
 * Cette fonction va regarder dans le tableau de validation du formulaire
 * Si nous avons un message (tab + index du tableau) alors nous ajoutons 
 * la class is-invalid.
 * Si pas de message et que nous avons bien fait un submit du formulaire
 * alors on ajoute la class is-valid.
 * 
 * Attention s'il n'y a pas de submit notre $formErrors = un tableau vide et pas
 * d'index isFormValid
 */
function getClassInputForm($formErrors, $index)
{
    if (isset($formErrors[$index])) {
        return 'is-invalid';
    } elseif (isset($formErrors['isFormValid'])) {
        return 'is-valid';
    };
}

function getMsgErrorForm($formErrors, $index)
{
    if (isset($formErrors[$index])) {
        return $formErrors[$index];
    } else {
        return '';
    }
}

function validationForm(): array
{
    //Initialisation du tableau
    /**
     * Par defaut nous avons un cas passant
     * cad le formulaire est ok et pas de msg d'erreur
     */
    $errorForm = [
        'isFormValid' => true,
        'errMsgLastname' => null,
        'errMsgMail' => null,
    ];

    /**
     * Test du lastname
     */
    if (strlen($_POST['lastname']) == 0) {
        $errorForm['isFormValid'] = false;
        $errorForm['errMsgLastname'] = 'Le nom ne peut pas être vide';
    }

    // test mail

    //Requete pour compter le nombre de mail existant pour celui du POST
    $sql = "SELECT count(id) as 'nbMail' FROM users WHERE mail = :post_mail AND id != :idUser";
    $stm = executeStatement($sql, [
        ':post_mail' => $_POST['mail'],
        ':idUser'   => $_GET['iduser']
    ]);
    $nbMail = $stm->fetch(PDO::FETCH_ASSOC);

    if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL) == false) {
        $errorForm['isFormValid'] = false;
        $errorForm['errMsgMail'] = 'Le format du courriel est invalide';
    } elseif ($nbMail['nbMail'] > 0) {
        //Si le nombre de mail est sup à 0
        $errorForm['isFormValid'] = false;
        $errorForm['errMsgMail'] = 'Ce courriel est déjà existant';
    }

    return $errorForm;
}

function updateUser()
{
    //Définir notre req
    $sql = "UPDATE `users` 
    SET `firstname`=:p_firstname,`lastname`=:p_lastname,`mail`=:p_mail,`birth`=:p_birth,`isInfected`=:p_isInfected, `isPublic`=:p_isPublic 
    WHERE id = :p_idUser";

    //Préparer notre req
    executeStatement($sql, [
        'p_firstname' => $_POST['firstname'],
        'p_lastname'  => $_POST['lastname'],
        'p_mail'      => $_POST['mail'],
        'p_birth'     => $_POST['birth'],
        'p_isInfected' => (int) isset($_POST['isInfected']),
        'p_idUser'      => $_GET['iduser'],
        'p_isPublic'    => (int) isset($_POST['isPublic'])
    ]);

    //Redirection vers index afin d'afficher notre nouvel user
    //header est une fonction de PHP pour modifier les entetes
    //Nous redirigeons vers index.html, mais cela pourrait être une autre page
    header('Location: modified.html?iduser='.$_GET['iduser']);
    exit();
}

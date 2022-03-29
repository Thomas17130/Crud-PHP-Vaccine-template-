<?php

//toujours avoir le session start pour utiliser les sessions sinon $_SESSION sera vide
session_start();

function executeStatement(string $sql, array $params=[]):PDOStatement{
    //Initialiser notre connexion avec la BDD
    $pdoConnect = new PDO(
        'mysql:host=localhost;dbname=tableau_vaccin;charset=UTF8',
        'root'
    );
    
    // Ajouter des options pour avoir les erreurs 
    // utilisation par défaut dans PHP 8
    // $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Préparer notre req
    $stm = $pdoConnect->prepare($sql);

    //executer notre req
    $stm->execute($params);

    return $stm;
}
?>
<?php
require_once 'connect.php';

function getUsers() : array
{
    //Définir notre req
    $sql = "SELECT id, firstname, lastname, birth, isInfected FROM users";

    $stm = executeStatement($sql);    

    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

function getInfectInfo(bool $status): array
{

    if ($status == true) {
        $infectClass = 'bg-danger';
        $infectLabel = 'Vous avez le COVID';
    } else {
        $infectClass = 'bg-success';
        $infectLabel = "Vous n'avez pas le COVID";
    }

    /**
     * On returne un tableau avec les éléments suivant : 
     * la class à utiliser
     * le libellé à utiliser
     */
    return [
        'class' => $infectClass,
        'label' => $infectLabel
    ];
}

function getFormatedDate(string $date): string
{
    $t = new DateTime($date);
    return $t->format('d-m-Y');
}

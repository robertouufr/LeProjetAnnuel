<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'projetannuelb1';

$con = new mysqli($host, $user, $pass, $db);


if ($con->connect_error) {
    die("Connexion Error !" . $con->connect_error);
}

$con->set_charset("utf8mb4");

?>
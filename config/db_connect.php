<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'gestion_stock';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}
?>

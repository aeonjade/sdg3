<?php
include("connection.php");

$documentType = $_POST['documentType'];

$sql = "DELETE FROM documents WHERE documentType=?";
$pdo->prepare($sql)->execute([$documentType]);

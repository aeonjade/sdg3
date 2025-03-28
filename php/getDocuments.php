<?php
include("connection.php");

$documents = $pdo->query("SELECT * FROM documents")->fetchAll();

<?php

include("connection.php");

$docType = $_POST['documentType'];
$applicantID = $_POST['applicantID'];

$sql = "UPDATE documents SET documentStatus = 'Approved' 
        WHERE applicantID = ? AND documentType = ?";
$pdo->prepare($sql)->execute([$applicantID, $docType]);

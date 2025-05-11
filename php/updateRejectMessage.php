<?php

include("connection.php");

$rejectReason = $_POST['rejectReason'];
$docType = $_POST['documentType'];
$applicantID = $_POST['applicantID'];

$sql = "UPDATE documents SET rejectReason = ?, documentStatus = 'Rejected' 
        WHERE applicantID = ? AND documentType = ?";
$pdo->prepare($sql)->execute([$rejectReason, $applicantID, $docType]);

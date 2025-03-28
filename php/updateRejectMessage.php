<?php 

include("connection.php");
$fileName = $_POST['fileName'];
$rejectReason = $_POST['rejectReason'];


$sql = "UPDATE documents SET rejectReason=? WHERE documentName=?";
$pdo->prepare($sql)->execute([$rejectReason, $fileName]);

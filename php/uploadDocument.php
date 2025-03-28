<?php
include("connection.php");

if (!empty($_FILES['file']['name'])) {
    $applicantID = $_POST['applicantID'];
    $documentName = $_POST['documentName'];
    $documentType = $_POST['documentType'];

    $target_dir = "../documents/";  // Directory where files will be stored
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Insert file details into database
        $sql = "INSERT INTO documents (applicantID, documentName, documentType) VALUES (?,?,?)";
        $pdo->prepare($sql)->execute([$applicantID, $documentName, $documentType]);
    }
}

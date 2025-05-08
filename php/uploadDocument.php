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

    // Get file extension
    $file_extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
    
    // Create unique filename using timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $original_filename = pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME);
    $new_filename = $original_filename . '_' . $timestamp . '.' . $file_extension;
    
    $target_file = $target_dir . $new_filename;

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Insert file details into database
        $sql = "INSERT INTO documents (applicantID, documentName, documentType) VALUES (?,?,?)";
        $pdo->prepare($sql)->execute([$applicantID, $documentName, $documentType]);
    }
}
<?php
include("connection.php");

if (!empty($_FILES['file']['name'])) {
    $applicantID = $_POST['applicantID'];
    $documentName = $_POST['documentName'];
    $documentType = $_POST['documentType'];

    $target_dir = "../documents/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
    $timestamp = date('Y-m-d_H-i-s');
    $original_filename = pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME);
    $new_filename = $original_filename . '_' . $timestamp . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    try {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO documents (applicantID, documentName, documentType) VALUES (?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$applicantID, $new_filename, $documentType]);
            
            // Return success response with filename
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'filename' => $new_filename
            ]);
        } else {
            throw new Exception("Failed to move uploaded file.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
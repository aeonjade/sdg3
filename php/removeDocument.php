<?php
header('Content-Type: application/json');
include("connection.php");

try {
    $applicantID = $_POST['applicantID'];
    $documentType = $_POST['documentType'];

    // Get the filename before deleting the record
    $stmt = $pdo->prepare("SELECT documentName FROM documents WHERE applicantID = ? AND documentType = ?");
    $stmt->execute([$applicantID, $documentType]);
    $document = $stmt->fetch();

    if ($document) {
        // Delete the physical file
        $filepath = "../documents/" . $document['documentName'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete the database record
        $stmt = $pdo->prepare("DELETE FROM documents WHERE applicantID = ? AND documentType = ?");
        $stmt->execute([$applicantID, $documentType]);

        echo json_encode([
            'success' => true,
            'message' => 'Document removed successfully'
        ]);
    } else {
        throw new Exception("Document not found");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
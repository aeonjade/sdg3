<?php
header('Content-Type: application/json');
include("connection.php");

try {
    $applicantID = $_POST['applicantID'];

    $hasRejected = false;
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE applicantID = ? AND documentStatus = 'rejected'");
    $stmt->execute([$applicantID]);
    $documents = $stmt->fetchAll();
    $hasRejected = count($documents) > 0;

    $reqStatus = $hasRejected ? 'incomplete' : 'accomplished';

    $stmt = $pdo->prepare("UPDATE applicants SET requirementsStatus = ? WHERE applicantID = ?");
    $stmt->execute([$reqStatus, $applicantID]);

    echo json_encode([
        'success' => true,
        'message' => 'Applicant requirement status updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

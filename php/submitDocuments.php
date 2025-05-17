<?php
header('Content-Type: application/json');
include("connection.php");

try {
    $applicantID = $_POST['applicantID'];

    $stmt = $pdo->prepare("UPDATE applicants SET requirementsStatus = 'submitted' WHERE applicantID = ?");
    $stmt->execute([$applicantID]);

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

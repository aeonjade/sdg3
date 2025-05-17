<?php
include("connection.php");

function getApplicants($where = '', $params = [])
{
    global $pdo;

    $sql = "SELECT * FROM applicants";
    if (!empty($where)) {
        $sql .= " WHERE {$where}";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

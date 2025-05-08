<?php
include("connection.php");

function getDocuments($where = '', $params = [])
{
    global $pdo;

    $sql = "SELECT * FROM documents";
    if (!empty($where)) {
        $sql .= " WHERE {$where}";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

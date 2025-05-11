<?php
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    $filepath = 'documents/' . $file;

    if (file_exists($filepath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
echo "File not found.";
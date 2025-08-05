<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$keys = [
    'ClientID' => 'a9y9zvde6rxmhputndsb3xo6bzuhwo',
    'AccessToken' => 'yeul8nafxmcwiqo59p4dqsx5counej'
];

$requestKey = $_GET['name'] ?? '';

if (array_key_exists($requestKey, $keys)) {
    header('Content-Type: application/json');
    echo json_encode([
        'key' => $keys[$requestKey]
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'error' => 'Invalid Key Name'
    ]);
}

?>
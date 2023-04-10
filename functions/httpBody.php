<?php

function verifyHttpBodyJSON($body) {
    if ($body === null) {
        http_response_code(400);
        header('Content-Type: application/json');
        exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
    }
    
    if (empty($body)) {
        http_response_code(400);
        header('Content-Type: application/json');
        exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
    }

    return true;
}
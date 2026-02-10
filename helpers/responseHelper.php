<?php
class ResponseHelper {
    public static function sendResponse($data = null, $message = '', $status = 200, $error = null) {
        http_response_code($status);
        
        $response = [
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($error !== null) {
            $response['error'] = $error;
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public static function sendError($message = 'An error occurred', $status = 500, $error = null) {
        self::sendResponse(null, $message, $status, $error);
    }
}
?>
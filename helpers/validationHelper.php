<?php
class ValidationHelper {
    public static function validateRequired($data, $fields) {
        $errors = [];
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = "The $field field is required";
            }
        }
        
        return $errors;
    }

    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }
        
        return htmlspecialchars(strip_tags(trim($data)));
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateNumber($number, $min = null, $max = null) {
        if (!is_numeric($number)) return false;
        if ($min !== null && $number < $min) return false;
        if ($max !== null && $number > $max) return false;
        return true;
    }
}
?>
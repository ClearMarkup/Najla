<?php

class Core
{
    static protected function applyOperations($value, $operations)
    {
        if (!is_string($operations)) {
            $operations = implode('|', $operations);
        }

        $operations = explode('|', $operations);

        foreach ($operations as $operation) {
            $parameter = null;

            if (strpos($operation, ':') !== false) {
                list($operation, $parameter) = explode(':', $operation);
            }

            switch ($operation) {
                case 'trim':
                    $value = trim($value);
                    break;
                case 'escape':
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    break;
                case 'empty_string_to_null':
                    $value = $value === '' ? null : $value;
                    break;
                case 'strip_tags':
                    $value = strip_tags($value);
                    break;
                case 'truncate':
                    $truncateLength = is_numeric($parameter) ? $parameter : 57;
                    $value = strlen($value) > $truncateLength ? substr($value, 0, $truncateLength) . '...' : $value;
                    break;
                case 'email':
                    $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                    break;
            }
        }

        return $value;
    }

    static function log($action, $data)
    {
        global $auth;
        $db = new Db();

        $db->table('users_logs')->insert([
            'user_id' => $auth->getUserId() ?? null,
            'action' => $action,
            'data' => json_encode($data),
            'created_at' => time()
        ]);

    }

    static function checkLog($action, $mot, $time)
    {
        global $auth, $database;
        $db = new Db();

        $time = strtotime($time) - time();

        $log = $database->count('users_logs', [
            'user_id' => $auth->getUserId(),
            'action' => $action,
            'created_at[>]' => time() - $time
        ]);

        if ($log >= $mot) {
            return true;
        } else {
            return false;
        }
    }
    
}
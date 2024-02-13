<?php

namespace Najla\Classes;

use Medoo\Medoo;
use Delight\Auth\Auth;

class Core
{
    protected static $dbInstance;
    protected static $authInstance;

    public function __construct()
    {
        global $config;
        self::$dbInstance = new Medoo($config->database);
        self::$authInstance = new Auth(self::$dbInstance->pdo, null, null, $config->debug ? false : true);
    }

    public static function getDbInstance()
    {
        global $config;
        if (self::$dbInstance === null) {
            self::$dbInstance = new Medoo($config->database);
        }
        return self::$dbInstance;
    }

    public static function getAuthInstance()
    {
        self::getDbInstance();
        global $config;
        if (self::$authInstance === null) {
            self::$authInstance = new Auth(self::$dbInstance->pdo, null, null, $config->debug ? false : true);
        }
        return self::$authInstance;
    }



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
        $db = new Db();

        $db->table('users_logs')->insert([
            'user_id' => self::$authInstance->getUserId() ?? null,
            'action' => $action,
            'data' => json_encode($data),
            'created_at' => time()
        ]);
    }

    static function checkLog($action, $mot, $time)
    {

        $time = strtotime($time) - time();

        $log = self::$dbInstance->count('users_logs', [
            'user_id' => self::$authInstance->getUserId(),
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

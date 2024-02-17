<?php

namespace ClearMarkup\Classes;

class View extends Core
{

    protected $data = [];

    public function __construct()
    {
        global $config, $match;

        $this->assign('site', [
            'name' => $config->sitename,
            'language' => explode('_', $config->locale)[0],
            'url' => $config->url,
            'version' => $config->version,
        ]);

        $db = new Db;
        if (self::$authInstance->isLoggedIn()) {
            $user = $db->table('users')->filter('id', self::$authInstance->getUserId())->get([
                'id',
                'email',
                'username',
                'status'
            ]);

            $user['needsEmailConfirmation'] = $db->table('users_confirmations')->filter([
                'user_id' => self::$authInstance->getUserId(),
                'expires[>]' => time()
            ])->has();
        } else {
            $user = false;
        }

        $this->assign('user', $user);
        $this->assign('page', [
            'name' => $match['name'] ?? null,
        ]);
    }

    public function assign($key, $value)
    {
        if (isset($this->data[$key]) && is_array($this->data[$key]) && is_array($value)) {
            $this->data[$key] = (object) array_merge($this->data[$key], $value);
        } else if (isset($this->data[$key]) && is_object($this->data[$key]) && is_array($value)) {
            $this->data[$key] = (object) array_merge((array) $this->data[$key], $value);
        } else {
            $this->data[$key] = is_array($value) ? (object) $value : $value;
        }
        return $this;
    }

    public function auth($status = true)
    {
        if ($status) {
            if (!self::$authInstance->isLoggedIn()) {
                header('Location: /login');
                exit;
            }
        } else {
            if (self::$authInstance->isLoggedIn()) {
                header('Location: /');
                exit;
            }
        }
        return $this;
    }

    public function isStatus($status)
    {
        switch ($status) {
            case 'normal':
                if (!self::$authInstance->isNormal()) {
                    http_response_code(403);
                    header('Location: /login');
                    exit;
                }
                break;
        }
        return $this;
    }

    public function hasRole($role)
    {
        if (!self::$authInstance->hasRole($role)) {
            http_response_code(403);
            header('Location: /login');
            exit;
        }
        return $this;
    }

    public function render($view, $status = 200)
    {
        extract($this->data);

        require(__DIR__ . '/../views/' . $view . '.view.php');
        return $this;
    }
}

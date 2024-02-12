<?php
namespace Najla\Classes;

class View extends Core
{

    protected $data = [];

    public function __construct()
    {
        global $config, $match;
        $this->assign('site', (object) [
            'name' => $config->sitename,
            'language' => $config->language,
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

        $this->assign('user', (object) $user);
        $this->assign('page', (object) [
            'name' => $match['name'] ?? null,
        ]);
    }

    public function assign($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function auth($status = true)
    {
        if ($status) {
            if (!self::$authInstance->isLoggedIn()) {
                header('Location: /' . __('urLogin'));
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
                    header('Location: /' . __('urLogin'));
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
            header('Location: /' . __('urLogin'));
            exit;
        }
        return $this;
    }

    public function render($view, $status = 200)
    {
        extract($this->data);

        $seo = json_decode(file_get_contents('lang/' . $site->language . '/' . $site->language . '.seo.json'), true);
        
        $page->title = $page->title ?? (isset($page->name) && isset($seo[$page->name]) ? $seo[$page->name]['title'] ?? $seo['title'] : $seo['title']);
        $page->Desc = $page->Desc ?? (isset($page->name) && isset($seo[$page->name]) ? $seo[$page->name]['description'] ?? $seo['description'] : $seo['description']);
        
        
        ob_start();
        require(__DIR__ . '/../views/' . $view . '.view.php');
        $output = ob_get_clean();
        
        
        $lang_list = json_decode(file_get_contents(__DIR__ . '/../lang/' . $site->language . '/' . $site->language . '.lang.json'), true);
        foreach ($lang_list as $key => $value) {
            $output = str_replace('{{' . $key . '}}', $value, $output);
        }

        http_response_code($status);
        echo $output;
        return $this;
    }
}

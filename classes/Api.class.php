<?php

class Api extends Core
{
    private $requestBody = [];

    public function csrf()
    {
        $_token = filter_input(INPUT_SERVER, 'HTTP_X_CSRF_TOKEN', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$_token || !isset($_SESSION['_token'])) {
            $this->error('Invalid CSRF token.', 405);
        } else if ($_token !== $_SESSION['_token']) {
            $this->error('Invalid CSRF token.', 405);
        }
        return $this;
    }

    public function auth($status = true)
    {
        global $auth;
        if ($status) {
            if (!$auth->isLoggedIn()) {
                $this->error('You are not logged in.');
            }
        } else {
            if ($auth->isLoggedIn()) {
                $this->error('You are already logged in.');
            }
        }
        return $this;
    }

    public function isStatus($status)
    {
        global $auth;
        switch ($status) {
            case 'normal':
                if (!$auth->isNormal()) {
                    $this->error('You are not authorized to access this resource.');
                }
                break;
        }
        return $this;
    }

    public function hasRole($role)
    {
        global $auth;
        if (!$auth->hasRole($role)) {
            $this->error('You are not authorized to access this resource.');
        }
        return $this;
    }

    public function requestBody($type = 'json')
    {
        if ($type === 'json') {
            $this->requestBody = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON payload.');
            }
        } else if ($type === 'get') {
            $this->requestBody = $_GET;
        } else if ($type === 'post') {
            $this->requestBody = $_POST;
        } else {
            $this->error('Invalid request body type.');
        }

        return $this;
    }

    public function validate($rules)
    {
        global $validator;
        $validation = $validator->validate($this->requestBody, $rules);
        if ($validation->fails()) {
            $this->error(['elements' => $validation->errors()->firstOfAll()]);
        }
        $this->requestBody = $validation->getValidData();
        return $this;
    }

    public function getBody($input = null)
    {
        if ($input) {
            if (is_array($input)) {
                $data = [];
                foreach ($input as $key) {
                    $data[$key] = $this->requestBody[$key];
                }
                return $data;
            } else {
                return $this->requestBody[$input];
            }
        }
        return $this->requestBody;
    }

    private function response($status, $data = [], $responseCode = 200)
    {
        http_response_code($responseCode);
        header('Content-Type: application/json');
        if (is_string($data)) {
            echo json_encode([
                'status' => $status,
                'message' => $data
            ]);
        } else {
            echo json_encode(array_merge(['status' => $status], $data));
        }
        exit;
    }

    public function success($data = [], $responseCode = 200)
    {
        $this->response('success', $data, $responseCode);
    }

    public function error($data = [], $responseCode = 200)
    {
        $this->response('error', $data, $responseCode);
    }
}

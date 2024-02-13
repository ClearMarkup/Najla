<?php
namespace Najla\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Tools extends Core{

    public function __construct()
    {
        parent::__construct();
    }

    static public function sanitize($value, $operations)
    {
        if (is_callable($operations)) {
            return $operations($value);
        } else {
            return self::applyOperations($value, $operations);
        }

        return $value;
    }

    static public function exlodeMap($value, $delimiter, $callback)
    {
        $value = explode($delimiter, $value);
        $value = array_map($callback, $value);
        return array_filter($value);
    }

    static public function shortNummber($n, $precision = 1)
    {
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'k';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'm';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'b';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 't';
        }
    
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
    
        return $n_format . $suffix;
    }

    static function sendEmail($to, $subject, $template, $holders = [])
    {
        global $config;
    
        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";
    
        if ($config->smtp) {
            $mail->isSMTP();
            $mail->Host       = $config->smtp['host'];
            $mail->SMTPAuth   = $config->smtp['SMTPAuth'];
            $mail->Username   = $config->smtp['username'];
            $mail->Password   = $config->smtp['password'];
            $mail->SMTPSecure = $config->smtp['SMTPSecure'];
            $mail->Port       = $config->smtp['port'];
        }
    
        $content = file_get_contents('lang/' . $config->language . '/email/' . $template . '.html');
    
        foreach ($holders as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
    
        $mail->setFrom($config->mail_from, $config->mail_from_text);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        try {
            $mail->send();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
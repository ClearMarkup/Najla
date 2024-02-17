<?php
namespace ClearMarkup\Classes;

class Exceptions
{
    public function getMessage($exception)
    {
        switch (get_class($exception)) {
            case 'Delight\Auth\InvalidEmailException':
            case 'Delight\Auth\InvalidPasswordException':
                return __('incorrectEmailOrPassword');
            case 'Delight\Auth\EmailNotVerifiedException':
                return __('emailNotVerified');
            case 'Delight\Auth\TooManyRequestsException':
                return __('tooManyRequests');
            default:
                return __('unknownError');
        }
    }
}
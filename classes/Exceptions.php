<?php
namespace ClearMarkup\Classes;

class Exceptions
{
    public function getMessage($exception)
    {
        switch (get_class($exception)) {
            case 'Delight\Auth\InvalidEmailException':
            case 'Delight\Auth\InvalidPasswordException':
                return _('Invalid email or password');
            case 'Delight\Auth\EmailNotVerifiedException':
                return _('Email not verified');
            case 'Delight\Auth\TooManyRequestsException':
                return _('Too many requests have been made');
            default:
                return _('An error occurred, please try again later.');
        }
    }
}
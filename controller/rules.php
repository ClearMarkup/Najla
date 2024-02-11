<?php
// Register custom validation rule for password confirmation
class CorrectPassword extends \Rakit\Validation\Rule
{
    protected $message = "Incorrect password. Please try again.";

    protected $fillableParams = [];

    public function check($value): bool
    {
        global $auth;
        return $auth->reconfirmPassword($value);
    }
}
$validator->addValidator('correct_password', new CorrectPassword());

// Register custom validation rule for password vaildation
class PasswordValidation extends \Rakit\Validation\Rule
{
    protected $message = "{{inavlid_pwd}}";

    protected $fillableParams = [];

    public function check($value): bool
    {
        global $config;

        $lengthRequirement = $config->password_policy['length'];
        $uppercaseRequirement = $config->password_policy['uppercase'];
        $lowercaseRequirement = $config->password_policy['lowercase'];
        $digitRequirement = $config->password_policy['digit'];
        $specialRequirement = $config->password_policy['special'];

        $errors = [];

        $length = strlen($value);
        $uppercaseCount = preg_match_all('/[A-Z]/', $value);
        $lowercaseCount = preg_match_all('/[a-z]/', $value);
        $digitCount = preg_match_all('/[0-9]/', $value);
        $specialCount = preg_match_all('/[^A-Za-z0-9]/', $value);

        if ($lengthRequirement > 0 && $length < $lengthRequirement) {
            $errors[] = "be at least $lengthRequirement characters long.";
        }

        if ($uppercaseRequirement > 0 && $uppercaseCount < $uppercaseRequirement) {
            $errors[] = "contain at least $uppercaseRequirement uppercase letter(s).";
        }

        if ($lowercaseRequirement > 0 && $lowercaseCount < $lowercaseRequirement) {
            $errors[] = "contain at least $lowercaseRequirement lowercase letter(s).";
        }

        if ($digitRequirement > 0 && $digitCount < $digitRequirement) {
            $errors[] = "contain at least $digitRequirement digit(s).";
        }

        if ($specialRequirement > 0 && $specialCount < $specialRequirement) {
            $errors[] = "contain at least $specialRequirement special character(s).";
        }

        if (empty($errors)) {
            return true;
        } else {
            $this->message = "The password must:<br> - ";
            $this->message .= implode("<br> - ", $errors);
            return false;
        }
    }
}
$validator->addValidator('password_validation', new PasswordValidation());

class UsernameValidation extends \Rakit\Validation\Rule
{
    protected $message = "The username must be between 5 and 20 characters long, only contain alphanumeric characters, numbers and underscores, and not start or end with an underscore.";

    protected $fillableParams = [];

    public function check($value): bool
    {
        global $database, $auth;

        $check = $database->has('users', ['username' => $value]);

        if ($check) {
            $this->message = "This username is already taken.";
            return false;
        }

        if (!\preg_match('/[\x00-\x1f\x7f\/:\\\\]/', $value) === 0) {
            return true;
        }

        // Check if the username is between 5 and 20 characters long
        if (strlen($value) < 5 || strlen($value) > 20) {
            return false;
        }

        // Check if the username only contains alphanumeric characters and underscores
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
            return false;
        }

        // Check if the username doesn't start or end with an underscore
        if ($value[0] === '_' || $value[strlen($value) - 1] === '_') {
            return false;
        }

        // Check if the username doesn't contain more than one underscore in a row
        if (strpos($value, '__') !== false) {
            return false;
        }

        return true;
    }
}

$validator->addValidator('username_validation', new UsernameValidation());

/* $validator->setMessages([
	'required' => '{{required}}',
	'email' => '{{email_valid}}',
    'alpha' => '{{alpha}}',
    'max' => '{{max}}',
    'min' => '{{min}}',
    'alpha_spaces' => '{{alpha_spaces}}',
    'same' => '{{pwd_not_match}}',
]); */
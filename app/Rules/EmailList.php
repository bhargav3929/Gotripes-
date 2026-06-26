<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Validates a free-text list of notification recipient emails
 * (comma / newline / semicolon separated). Empty is allowed; the field
 * is meant to be optional and fall back to the company email.
 */
class EmailList implements Rule
{
    protected int $max;
    protected string $message = 'Please enter valid email addresses separated by commas.';

    public function __construct(int $max = 10)
    {
        $this->max = $max;
    }

    public function passes($attribute, $value): bool
    {
        if ($value === null || trim((string) $value) === '') {
            return true;
        }

        $tokens = preg_split('/[\s,;]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($tokens) > $this->max) {
            $this->message = "Please enter at most {$this->max} email addresses.";
            return false;
        }

        foreach ($tokens as $token) {
            if (!filter_var(trim($token), FILTER_VALIDATE_EMAIL)) {
                $this->message = "\"{$token}\" is not a valid email address.";
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return $this->message;
    }
}

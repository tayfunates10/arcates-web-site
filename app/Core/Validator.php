<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ((array) $fieldRules as $rule) {
                [$name, $arg] = array_pad(explode(':', (string) $rule, 2), 2, null);
                if ($name === 'required' && ($value === null || trim((string) $value) === '')) {
                    $this->errors[$field][] = 'required';
                } elseif ($name === 'email' && $value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $this->errors[$field][] = 'email';
                } elseif ($name === 'min' && mb_strlen((string) $value) < (int) $arg) {
                    $this->errors[$field][] = 'min';
                } elseif ($name === 'max' && mb_strlen((string) $value) > (int) $arg) {
                    $this->errors[$field][] = 'max';
                }
            }
        }
        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

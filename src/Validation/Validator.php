<?php

namespace Wasf\Validation;

use Wasf\Database\DB;

class Validator
{
    protected array $errors = [];

    public function validate(array $data, array $rules): array
    {
        foreach ($rules as $field => $ruleString) {
            $rulesArr = explode('|', $ruleString);

            foreach ($rulesArr as $rule) {

                // handle rule with params: min:3, max:100, unique:users,email
                $params = null;
                if (strpos($rule, ':') !== false) {
                    [$rule, $params] = explode(':', $rule, 2);
                }

                $value = $data[$field] ?? null;

                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            $this->addError($field, "$field wajib diisi.");
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, "Format $field tidak valid.");
                        }
                        break;

                    case 'min':
                        if (strlen($value) < (int)$params) {
                            $this->addError($field, "$field minimal $params karakter.");
                        }
                        break;

                    case 'max':
                        if (strlen($value) > (int)$params) {
                            $this->addError($field, "$field maksimal $params karakter.");
                        }
                        break;

                    case 'unique':
                        // unique:users,email
                        [$table, $column] = explode(',', $params);

                        // Temukan model berdasarkan nama table
                        $modelClass = model_from_table($table);

                        if (!$modelClass || !class_exists($modelClass)) {
                            $this->addError($field, "Model untuk tabel {$table} tidak ditemukan.");
                            break;
                        }

                        // Gunakan ORM QueryBuilder
                        $exists = $modelClass::where($column, $value)->first() !== null;

                        if ($exists) {
                            $this->addError($field, "$field sudah digunakan.");
                        }
                        break;
                }
            }
        }

        return $this->errors;
    }

    protected function addError(string $field, string $message)
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

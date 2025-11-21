<?php
namespace Wasf\Auth;

use Wasf\Database\DB;

class ModelProvider implements AuthProviderInterface
{
    protected string $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function findById($id)
    {
        if (!$id) return null;
        return $this->modelClass::find($id);
    }

    public function findByCredentials(array $credentials)
    {
        // credentials typically ['email' => '...', 'password' => '...']
        // We'll find by email (all keys except password)
        $c = $credentials;
        $password = $c['password'] ?? null;
        unset($c['password']);

        // Build where by first remaining credential
        foreach ($c as $col => $val) {
            $qb = $this->modelClass::where($col, $val);
            $user = $qb->first();
            if ($user) {
                // verify password if provided
                if ($password !== null) {
                    if (password_verify($password, $user->password)) return $user;
                    return null;
                }
                return $user;
            }
        }
        return null;
    }

    public function updateRememberToken($user, $token): void
    {
        if (method_exists($user, 'setRememberToken')) {
            $user->setRememberToken($token);
            return;
        }

        // best-effort: try updating column 'remember_token'
        if (property_exists($user, 'id')) {
            $table = $this->modelClass::table();
            $sql = "UPDATE {$table} SET remember_token = :token WHERE id = :id";
            $stmt = DB::pdo()->prepare($sql);
            $stmt->execute(['token' => $token, 'id' => $user->id]);
        }
    }
}

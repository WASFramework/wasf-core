<?php
namespace Wasf\Auth;

interface AuthProviderInterface
{
    /** Find user by primary id */
    public function findById($id);

    /** Find user by credentials (associative array) */
    public function findByCredentials(array $credentials);

    /** Optional: update remember token */
    public function updateRememberToken($user, $token): void;
}

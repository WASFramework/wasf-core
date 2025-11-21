<?php
namespace Wasf\Auth;

class AuthManager
{
    protected static ?AuthManager $instance = null;
    protected array $guards = [];
    protected string $defaultGuard = 'web';

    public static function instance(): AuthManager
    {
        if (!static::$instance) {
            static::$instance = new AuthManager();
        }
        return static::$instance;
    }

    public function setDefaultGuard(string $name): void
    {
        $this->defaultGuard = $name;
    }

    public function guard(string $name = null): SessionGuard
    {
        $name = $name ?? $this->defaultGuard;
        if (!isset($this->guards[$name])) {
            throw new \RuntimeException("Guard {$name} is not registered.");
        }
        return $this->guards[$name];
    }

    public function registerGuard(string $name, SessionGuard $guard): void
    {
        $this->guards[$name] = $guard;
    }

    // convenience helpers

    public function check(): bool
    {
        return $this->guard()->check();
    }

    public function user()
    {
        return $this->guard()->user();
    }

    public function id()
    {
        return $this->guard()->id();
    }

    public function login($user, bool $remember = false): void
    {
        $this->guard()->login($user, $remember);
    }

    public function logout(): void
    {
        $this->guard()->logout();
    }

    public function attempt(array $credentials, bool $remember = false)
    {
        return $this->guard()->attempt($credentials, $remember);
    }
}

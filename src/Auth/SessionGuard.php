<?php
namespace Wasf\Auth;

class SessionGuard
{
    protected AuthProviderInterface $provider;
    protected string $sessionKey = 'auth_user_id';
    protected ?object $user = null;

    public function __construct(AuthProviderInterface $provider)
    {
        $this->provider = $provider;
        $this->loadFromSession();
    }

    protected function loadFromSession(): void
    {
        $id = $_SESSION[$this->sessionKey] ?? null;
        if ($id) {
            $this->user = $this->provider->findById($id);
            if (!$this->user) {
                // stale session, clear
                unset($_SESSION[$this->sessionKey]);
            }
        }
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user->id ?? null;
    }

    public function login($user, bool $remember = false): void
    {
        // Regenerate session id for security
        if (function_exists('session_regenerate_id')) {
            session_regenerate_id(true);
        }

        $_SESSION[$this->sessionKey] = $user->id;
        $this->user = $user;

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->provider->updateRememberToken($user, $token);
            setcookie('remember', $user->id . '|' . $token, time() + (60 * 60 * 24 * 30), '/');
        }
    }

    public function logout(): void
    {
        unset($_SESSION[$this->sessionKey]);
        $this->user = null;
        if (isset($_COOKIE['remember'])) {
            setcookie('remember', '', time() - 3600, '/');
        }
    }

    public function attempt(array $credentials, bool $remember = false)
    {
        $user = $this->provider->findByCredentials($credentials);
        if ($user) {
            $this->login($user, $remember);
            return $user;
        }
        return null;
    }
}

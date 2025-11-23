<?php

namespace Wasf\Filesystem;

class StorageDisk
{
    protected string $name;
    protected array $config;

    public function __construct(string $name, array $config)
    {
        $this->name   = $name;
        $this->config = $config;
    }

    protected function diskRoot(): string
    {
        return base_path() . '/' . ltrim($this->config['root'], '/');
    }

    protected function rootPath(): string
    {
        $root = rtrim($this->diskRoot(), '/');
        return $root;
    }

    protected function ensureDirectoryExists(string $path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * Store raw contents into a file.
     */
    public function put(string $path, string $contents): string
    {
        $full = $this->rootPath() . '/' . ltrim($path, '/');
        $this->ensureDirectoryExists(dirname($full));

        file_put_contents($full, $contents);

        return $path;
    }

    /**
     * Upload file ARRAY (from $_FILES)
     */
    public function putFile(string $dir, array $file)
    {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = uniqid() . '.' . $ext;

        // Root berdasarkan disk
        $root = $this->rootPath(); // public/uploads
        $targetDir = rtrim($root, '/') . '/' . trim($dir, '/');

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $target = $targetDir . '/' . $name;

        if (move_uploaded_file($file['tmp_name'], $target)) {

            // Generate relative URL → /uploads/profile/namafile.png
            $relative = rtrim($this->config['url'], '/')       // "/uploads"
                        . '/'
                        . trim($dir, '/')                      // "profile"
                        . '/'
                        . $name;                               // "abc123.png"

            return $relative;
        }

        return null;
    }

    /**
     * Upload file with custom filename
     */
    public function putFileAs(string $dir, array $file, string $filename): string
    {
        $dir = trim($dir, '/');
        $fullDir = $this->rootPath() . '/' . $dir;

        $this->ensureDirectoryExists($fullDir);

        $path = "{$dir}/{$filename}";
        $full = $this->rootPath() . '/' . $path;

        move_uploaded_file($file['tmp_name'], $full);

        return $filename;
    }

    public function exists(string $path): bool
    {
        return file_exists($this->rootPath() . '/' . $path);
    }

    public function delete(string $path): bool
    {
        $full = $this->rootPath() . '/' . $path;

        return file_exists($full) ? unlink($full) : false;
    }

    public function path(string $path): string
    {
        return $this->rootPath() . '/' . ltrim($path, '/');
    }

    public function url(string $path): string
    {
        $prefix = $this->config['url'] ?? '';

        return rtrim($prefix, '/') . '/' . ltrim($path, '/');
    }

    public function safeDelete(string $path): bool
    {
        $full = $this->rootPath() . '/' . ltrim($path, '/');

        // Jika tidak ada ya sudah
        if (!file_exists($full)) {
            return false;
        }

        // Pastikan PHP tidak cache info file
        clearstatcache(true, $full);

        // Coba 3 kali (Windows kadang lock file sebentar)
        $attempts = 3;
        while ($attempts > 0) {

            // Buang referensi file yang masih nyangkut
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }

            // Coba hapus
            if (@unlink($full)) {
                return true;
            }

            // Retry berikutnya
            usleep(120000); // 120ms
            $attempts--;
        }

        // Fallback terakhir — rename jadi file .deleted
        $fallback = $full . '.deleted';
        @rename($full, $fallback);

        return !file_exists($full);
    }

}

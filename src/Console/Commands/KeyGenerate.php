<?php

namespace Wasf\Console\Commands;

class KeyGenerate extends Command
{
    public function signature(): string 
    { 
        return 'key:generate'; 
    }

    public function description(): string 
    { 
        return 'Generate a new WASF_KEY for the application'; 
    }

    public function handle(array $args): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error(".env file not found!");
            return;
        }

        $key = base64_encode(random_bytes(32));
        $formatted = "base64:" . $key;

        $env = file_get_contents($envPath);

        if (strpos($env, 'WASF_KEY=') !== false) {
            // Replace old key
            $env = preg_replace('/WASF_KEY=.*$/m', "WASF_KEY={$formatted}", $env);
        } else {
            // Append new key
            $env .= "\nWASF_KEY={$formatted}\n";
        }

        file_put_contents($envPath, $env);

        $this->info("New WASF_KEY generated successfully.");
        $this->line("Your key is: {$formatted}");
    }
}

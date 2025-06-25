<?php

namespace dansware03\laravelinstaller\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use dansware03\laravelinstaller\InstallerManager;

class ResetInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the installation status';

    /**
     * The installer manager instance.
     *
     * @var InstallerManager
     */
    protected $installer;

    /**
     * Create a new command instance.
     */
    public function __construct(InstallerManager $installer)
    {
        parent::__construct();
        $this->installer = $installer;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting Laravel Installer status and project state...');

        // 1. Eliminar el archivo de bloqueo de instalación
        $lockFile = config('installer.installer_lock_file', storage_path('installed'));
        if (File::exists($lockFile)) {
            File::delete($lockFile);
            $this->line('<fg=green>[✔]</> Installation lock file removed.');
        } else {
            $this->line('<fg=yellow>[INFO]</> No installation lock file found.');
        }

        // 2. Restaurar .env desde .env.example
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        if (File::exists($envExamplePath)) {
            try {
                File::copy($envExamplePath, $envPath);
                $this->line('<fg=green>[✔]</> .env file has been reset from .env.example.');

                // 3. Generar nueva APP_KEY
                $this->call('key:generate', ['--force' => true]);
                $this->line('<fg=green>[✔]</> New APP_KEY generated.');

            } catch (\Exception $e) {
                $this->error('[✘] Failed to reset .env file or generate APP_KEY: ' . $e->getMessage());
            }
        } else {
            $this->warn('[!] .env.example not found. Skipping .env reset and key generation.');
        }

        // 4. Restaurar routes/api.php
        $apiRoutesPath = base_path('routes/api.php');
        $disabledApiRoutesPath = base_path('routes/api.php.disabled');

        if (File::exists($disabledApiRoutesPath)) {
            try {
                File::move($disabledApiRoutesPath, $apiRoutesPath);
                $this->line('<fg=green>[✔]</> routes/api.php has been restored.');
            } catch (\Exception $e) {
                $this->error('[✘] Failed to restore routes/api.php: ' . $e->getMessage());
            }
        } elseif (File::exists($apiRoutesPath)) {
             $this->line('<fg=yellow>[INFO]</> routes/api.php already exists and no .disabled version found. No action taken.');
        } else {
            $this->warn('[!] Neither routes/api.php nor routes/api.php.disabled found. Cannot restore API routes.');
        }
        
        // 5. Restaurar bootstrap/app.php (revertir comentario de API)
        $bootstrapPath = base_path('bootstrap/app.php');
        if (File::exists($bootstrapPath)) {
            try {
                $content = File::get($bootstrapPath);
                $commentedLine = '// API disabled by installer' . PHP_EOL . '    ->withRouting(';
                $originalLine = '    ->withRouting('; // Asegurar que coincida con la indentación original

                if (str_contains($content, $commentedLine)) {
                    $content = str_replace($commentedLine, $originalLine, $content);
                    File::put($bootstrapPath, $content);
                    $this->line('<fg=green>[✔]</> API routing in bootstrap/app.php has been re-enabled.');
                } else {
                    $this->line('<fg=yellow>[INFO]</> API routing in bootstrap/app.php was not previously disabled by the installer or already re-enabled.');
                }
            } catch (\Exception $e) {
                $this->error('[✘] Failed to modify bootstrap/app.php: ' . $e->getMessage());
            }
        } else {
            $this->warn('[!] bootstrap/app.php not found. Skipping modification.');
        }

        $this->info('Project reset process completed.');
        return Command::SUCCESS;
    }
}
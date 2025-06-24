<?php

namespace Dansware03\LaravelInstaller\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ResetInstallCommand extends Command
{
    protected $signature = 'installer:reset';
    protected $description = 'Reset the installation status';

    public function handle()
    {
        $lockFile = config('installer.installer_lock_file');
        
        if (File::exists($lockFile)) {
            File::delete($lockFile);
            $this->info('Installation status reset successfully.');
        } else {
            $this->info('No installation lock file found.');
        }
        
        return 0;
    }
}
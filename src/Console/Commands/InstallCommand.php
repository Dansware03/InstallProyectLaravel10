<?php

namespace Dansware03\LaravelInstaller\Console\Commands;

use Illuminate\Console\Command;
use Dansware03\LaravelInstaller\InstallerManager;

class InstallCommand extends Command
{
    protected $signature = 'installer:install';
    protected $description = 'Install the application via command line';

    protected $installer;

    public function __construct(InstallerManager $installer)
    {
        parent::__construct();
        $this->installer = $installer;
    }

    public function handle()
    {
        $this->info('Laravel Installer Command');
        $this->info('This will guide you through the installation process.');
        
        if ($this->installer->isInstalled()) {
            $this->error('Application is already installed!');
            return 1;
        }

        $this->info('Installation completed via web interface is recommended.');
        $this->info('Visit /install in your browser to start the installation.');
        
        return 0;
    }
}
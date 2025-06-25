<?php

namespace dansware03\laravelinstaller\Console\Commands;

use Illuminate\Console\Command;
use dansware03\laravelinstaller\InstallerManager;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel application using command line';

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
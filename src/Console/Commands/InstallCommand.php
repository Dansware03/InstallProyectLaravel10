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
    protected $description = 'Provides guidance on how to start the web-based installation process.';

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

        $this->line("--------------------------------------------------------------------");
        $this->line(" Laravel Application Installer ");
        $this->line("--------------------------------------------------------------------");
        $this->comment("This command does not perform the installation via command line.");
        $this->comment("To install your Laravel application, please use the web interface.");
        $this->info("1. Ensure your web server (e.g., Nginx, Apache) is running and configured.");
        $this->info("2. Open your web browser and navigate to your application's URL followed by '/install'.");
        $this->info("   Example: http://your-app-domain.test/install");
        $this->line("Follow the on-screen instructions to complete the installation.");
        
        return 0;
    }
}
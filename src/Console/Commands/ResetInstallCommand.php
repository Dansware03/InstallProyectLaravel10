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
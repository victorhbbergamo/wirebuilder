<?php

namespace Coffeemosele\Wirebuilder\Console;

use Coffeemosele\Wirebuilder\Facades\Wirebuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'wirebuilder:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Wirebuilder package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    public function handle()
    {

        if (Wirebuilder::configNotPublished()) {
            return $this->warn(
                'Please publish the config file by running ' .
                    '\'php artisan vendor:publish --tag=wirebuilder-config\''
            );
        }

        $this->initComponents();
    }

    /**
     * Init components
     * - make directories
     * - copy files to directories
     * 
     * @return void
     */
    public function initComponents()
    {
        $this->directory = config('wirebuilder.directory');

        if (is_dir($this->directory)) {
            $this->error("{$this->directory} directory already exists !");

            if (!$this->confirm('Do you want to copy the components anyway?')) {
                return;
            }
        }

        if ($this->copyFilesFromPackage()) {
            $this->info('Files created successfully [' . str_replace(base_path(), '', $this->directory) . ']');
            return;
        }

        $this->error('Files cannot be moved.');
    }

    /**
     * Copy files.
     *
     * @param string $path
     * 
     * @return bool
     */
    protected function copyFilesFromPackage()
    {
        $resources = dirname(__DIR__, 2) . '/resources/views/components';

        return File::copyDirectory($resources, $this->directory);
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory("{$this->directory}/$path", 0755, true, true);
    }
}

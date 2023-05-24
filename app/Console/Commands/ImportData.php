<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportData extends Command
{
    protected $signature = 'import:data {file}';
    protected $description = 'Import data from a file';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');
        // Your import logic goes here
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployCommand extends Command
{
    protected $signature = 'rayka:deploy {--seed : Run database seeder as well}';

    protected $description = 'One-click deployment script: run migrations, setup storage/backups, and clear caches for Hostinger / ServerByte.';

    public function handle(): int
    {
        $this->info('🚀 Starting Rayka Production Deployment...');

        // 1. Run migrations
        $this->info('Step 1: Running Database Migrations...');
        $this->call('migrate', ['--force' => true]);

        // 2. Setup storage and external directories
        $this->info('Step 2: Setting up storage and backup directories...');
        $this->call('rayka:setup-storage');

        // 3. Optional Seeder
        if ($this->option('seed')) {
            $this->info('Step 3: Running Database Seeders...');
            $this->call('db:seed', ['--force' => true]);
        }

        // 4. Optimize / Clear cache
        $this->info('Step 4: Clearing and refreshing application caches...');
        $this->call('optimize:clear');

        $this->info('✨ Rayka deployment completed successfully!');

        return Command::SUCCESS;
    }
}

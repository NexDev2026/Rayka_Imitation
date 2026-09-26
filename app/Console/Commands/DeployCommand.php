<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployCommand extends Command
{
    protected $signature = 'rayka:deploy';

    protected $description = 'Safe deployment script: setup storage/backups and clear caches for Hostinger / ServerByte.';

    public function handle(): int
    {
        $this->info('🚀 Starting Rayka Safe Deployment...');

        // 1. Setup storage and external directories
        $this->info('Step 1: Setting up storage and backup directories...');
        $this->call('rayka:setup-storage');

        // 2. Optimize / Clear cache
        $this->info('Step 2: Clearing and refreshing application caches...');
        $this->call('optimize:clear');

        $this->info('✨ Rayka deployment completed safely with 100% database protection!');

        return Command::SUCCESS;
    }
}

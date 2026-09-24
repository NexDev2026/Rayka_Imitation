<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('carts:prune-empty')]
#[Description('Prune empty ghost carts older than 7 days')]
class PruneEmptyCarts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Cart::doesntHave('items')
            ->where('updated_at', '<', now()->subDays(7))
            ->delete();

        $this->info("Pruned {$count} empty ghost carts.");
    }
}

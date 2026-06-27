<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class PrePushCleanup extends Command
{
    protected $signature = 'app:pre-push';
    protected $description = 'Clean up local caches, Filament assets, and compile front-end before pushing to GitHub.';

    public function handle()
    {
        $this->info('🚀 Starting pre-push optimization...');

        // 1. Clear all Laravel framework caches
        $this->warn('🧹 Clearing Laravel caches...');
        $this->call('optimize:clear');

        // 2. Clear Filament upgrades cache if applicable
        if (class_exists(\Filament\Support\Commands\UpgradeCommand::class)) {
            $this->warn('🎨 Clearing Filament upgrade artifacts...');
            $this->call('filament:upgrade');
        }

        // 3. Run production asset compilation
        $this->warn('📦 Compiling front-end assets via Vite...');

        // Using Laravel 10/11 native process runner
        $result = Process::run('npm run build');

        if ($result->successful()) {
            $this->info($result->output());
            $this->info('✅ Clean-up complete! You are ready to git add, commit, and push.');
        } else {
            $this->error('❌ Vite compilation failed:');
            $this->error($result->errorOutput());
        }
    }
}

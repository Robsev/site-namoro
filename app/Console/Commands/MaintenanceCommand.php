<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance {action : on|off} {--ip= : IP address to allow during maintenance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable maintenance mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $ip = $this->option('ip');

        if ($action === 'on') {
            $this->enableMaintenanceMode($ip);
        } elseif ($action === 'off') {
            $this->disableMaintenanceMode();
        } else {
            $this->error('Invalid action. Use "on" or "off".');
            return 1;
        }

        return 0;
    }

    /**
     * Enable maintenance mode
     */
    private function enableMaintenanceMode($ip = null)
    {
        $envFile = base_path('.env');
        
        if (File::exists($envFile)) {
            $envContent = File::get($envFile);
            
            // Update MAINTENANCE_MODE
            $envContent = preg_replace('/MAINTENANCE_MODE=.*/', 'MAINTENANCE_MODE=true', $envContent);
            
            // Add MAINTENANCE_MODE if it doesn't exist
            if (!str_contains($envContent, 'MAINTENANCE_MODE=')) {
                $envContent .= "\nMAINTENANCE_MODE=true\n";
            }
            
            // Update or add allowed IPs
            if ($ip) {
                $envContent = preg_replace('/MAINTENANCE_ALLOWED_IPS=.*/', "MAINTENANCE_ALLOWED_IPS={$ip}", $envContent);
                if (!str_contains($envContent, 'MAINTENANCE_ALLOWED_IPS=')) {
                    $envContent .= "MAINTENANCE_ALLOWED_IPS={$ip}\n";
                }
            } else {
                $envContent = preg_replace('/MAINTENANCE_ALLOWED_IPS=.*/', 'MAINTENANCE_ALLOWED_IPS=127.0.0.1,::1', $envContent);
                if (!str_contains($envContent, 'MAINTENANCE_ALLOWED_IPS=')) {
                    $envContent .= "MAINTENANCE_ALLOWED_IPS=127.0.0.1,::1\n";
                }
            }
            
            File::put($envFile, $envContent);
        }

        $this->info('Maintenance mode enabled successfully!');
        $this->line('Users will see the maintenance page.');
        
        if ($ip) {
            $this->line("IP {$ip} is allowed to access the site during maintenance.");
        }
    }

    /**
     * Disable maintenance mode
     */
    private function disableMaintenanceMode()
    {
        $envFile = base_path('.env');
        
        if (File::exists($envFile)) {
            $envContent = File::get($envFile);
            $envContent = preg_replace('/MAINTENANCE_MODE=.*/', 'MAINTENANCE_MODE=false', $envContent);
            File::put($envFile, $envContent);
        }

        $this->info('Maintenance mode disabled successfully!');
        $this->line('The site is now accessible to all users.');
    }
}

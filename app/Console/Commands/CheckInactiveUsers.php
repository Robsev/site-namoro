<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInactivityWarningMail;
use App\Mail\UserInactivityBlockedMail;

class CheckInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-inactivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for inactive users and send warnings or block accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        
        // Users inactive for 55 days (5 days before blocking) - Send warning
        $warningDate = $today->copy()->subDays(55);
        $usersToWarn = User::where('last_seen', '<=', $warningDate)
            ->where('is_active', true)
            ->whereNull('inactivity_warning_sent')
            ->get();
        
        foreach ($usersToWarn as $user) {
            $this->info("Sending inactivity warning to user: {$user->email}");
            
            try {
                Mail::to($user->email)->send(new UserInactivityWarningMail($user));
                $user->update(['inactivity_warning_sent' => now()]);
                $this->info("Warning sent successfully to {$user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send warning to {$user->email}: {$e->getMessage()}");
            }
        }
        
        // Users inactive for 60 days - Block account
        $blockDate = $today->copy()->subDays(60);
        $usersToBlock = User::where('last_seen', '<=', $blockDate)
            ->where('is_active', true)
            ->get();
        
        foreach ($usersToBlock as $user) {
            $this->info("Blocking inactive user: {$user->email}");
            
            try {
                $user->update(['is_active' => false]);
                
                // Send notification email
                Mail::to($user->email)->send(new UserInactivityBlockedMail($user));
                $this->info("Account blocked and notification sent to {$user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to block user {$user->email}: {$e->getMessage()}");
            }
        }
        
        $this->info("Inactivity check completed. Warned: {$usersToWarn->count()}, Blocked: {$usersToBlock->count()}");
        
        return Command::SUCCESS;
    }
}

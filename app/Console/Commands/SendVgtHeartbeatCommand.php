<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\VgtHeartbeatService;
use Illuminate\Console\Command;

class SendVgtHeartbeatCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vgt:heartbeat';

    /**
     * The console command description.
     */
    protected $description = 'Send telemetry and heartbeat signal to VGT Core Control Plane';

    /**
     * Execute the console command.
     */
    public function handle(VgtHeartbeatService $service): int
    {
        $this->info('Sending heartbeat to VGT Core...');

        $result = $service->sendHeartbeat();

        if ($result['success']) {
            $this->info("Heartbeat acknowledged: {$result['status']}");

            return Command::SUCCESS;
        }

        $this->warn("Heartbeat warning: {$result['status']} - ".($result['message'] ?? ''));

        return Command::SUCCESS; // Return success so scheduler doesn't report failure
    }
}

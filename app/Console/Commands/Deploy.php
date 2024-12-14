<?php

namespace App\Console\Commands;

use App\Models\DeployHistory;
use App\Models\Webhook;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:run {token} {trigger}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run pull latest image and restart container';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = $this->argument('token');
        $trigger = $this->argument('trigger');
        $webhook = Webhook::where('token', $token)->first();
        if(!$webhook) {
            $message = 'Webhook not found';
            $this->notifyFailed($message);
            $this->error($message);
        }
        $container  = $webhook->container_name;
        $image  = $webhook->image_name;

        $history = DeployHistory::create([
            'webhook_id' => $webhook->id,
            'status' => 'running',
            'trigger' => $trigger,
            'message' => "Pulling the latest Docker image: $image"
        ]);

        $this->info("Pulling the latest Docker image: $image");

        $process = new Process(['docker', 'pull', $image]);
        $process->setTimeout(null);

        try {
            $process->mustRun();

            $history->update(['status' => 'running', 'message' => "Restarting the container: $container"]);
            $process = new Process(['docker', 'stop', $container]);
            $process->setTimeout(null);
            $process->mustRun();

            $process = new Process(['docker', 'start', $container]);
            $process->setTimeout(null);
            $process->mustRun();

            $message = "Successfully pulled the Docker image: $image and restarted the container: $container";
            $this->info($message);
            $this->notifySuccess($message);
            $history->update(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            $message = "Failed to pull the Docker image: " . $e->getMessage();
            $this->notifyFailed($message);
            $history->update(['status' => 'failed', 'message' => $message]);
            $this->error($message);
        }
        
    }

    public function notifySuccess($message) {
        Notification::make()
                ->title('Deploy successfully')
                ->icon('heroicon-o-x-circle')
                ->body($message)
                ->iconColor('danger')
                ->send();
    }

    public function notifyFailed($message) {
        Notification::make()
                ->title('Deploy failed')
                ->icon('heroicon-o-x-circle')
                ->body($message)
                ->iconColor('danger')
                ->send();
    }
}

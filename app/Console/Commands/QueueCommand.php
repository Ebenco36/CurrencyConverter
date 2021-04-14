<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class QueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send currency status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $status = dispatch(new \App\Jobs\QueueNotification())->onQueue('high')->delay(now()->addSeconds(10));
		Log::info('Done sending status');
    }
}

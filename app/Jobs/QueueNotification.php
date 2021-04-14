<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //  get all user's base currency and validate against fixerAPI
		
		$allBaseCurrencies = \App\UserCurrency::Active()->get();
		foreach($allBaseCurrencies as $currency){
			
			$endpoint = 'latest';
			$baseCurrency = $currency->baseCurrency ? $currency->baseCurrency : 'USD';
			$responseCurrency = $currency->compared_to ? explode(', ', $currency->compared_to) : ['GBP'];
			// On premium
			// $rates =  $this->fixerApi->FixerLatestRates($endpoint, $baseCurrency, $responseCurrency);
			// Using the free
			$rates =  $this->fixerApi->FixerCurrencies($endpoint);
			
			if($rates->success){
				$process = @json_decode(json_encode($rates->rates), true);
				
				if(floatval($process[$currency->compared_to]) <= floatval($currency->threshold)){
					$details = $currency->user;
					$details['metrics'] = $process[$currency->compared_to];
					$details['compared_to'] = $currency->compared_to;
					$details['currency'] = $currency->currency;
					$details['threshold'] = $currency->threshold;
					dispatch(new \App\Jobs\SendEmailCurrencyNotification($details));
					echo 'done';
				}
				else{
					echo $process[$currency->compared_to].' is not up to '. $currency->threshold.' yet';
				}
			}	
			
		}
    }
}

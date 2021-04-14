<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\fixerAPI;
use Validator;
use JWTAuth;
class CurrencyConverterController extends Controller
{
	
	protected $fixerApi;
	
	public function __construct(){
		
		$this->fixerApi = new fixerAPI();
		
	}
	
	
	public function listCurrencies(){
		$endpoint = 'latest';
		$lists =  $this->fixerApi->FixerCurrencies($endpoint);
		return response()->json([
                'success' => true,
                'message' => 'Successful',
                'data' => $lists
        ], 200);
	}
	
	
    public function index(Request $request)
    {	
		$endpoint = 'latest';
		$baseCurrency = $request->baseCurrency ? $request->baseCurrency : 'USD';
		$responseCurrency = $request->responseCurrency ? explode(', ', $request->responseCurrency) : ['GBP', 'JPY', 'EUR'];
        $rates =  $this->fixerApi->FixerLatestRates($endpoint, $baseCurrency, $responseCurrency);
        if($rates->success){
            return response()->json([
                'success' => true,
                'message' => 'Successful',
                'banks' => $rates
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => $rates
            ], 404);
        }
    }
	
	public function setBaseCurrency(Request $request){
		
		$validator = Validator::make($request->all(),[
            'currency' => 'required|string',
			'compared_to' => 'required|string',
			'threshold' => 'required|numeric',
        ]);
		if ($validator->fails()) {
            $error = response()->json(['error'=>$validator->errors()], 422);
            return $error;
        }else{
			
			$user = JWTAuth::parseToken()->authenticate();
			$checkUserEntry = \App\UserCurrency::where('user_id', $user->id)->first();
			if($checkUserEntry){
				$checkUserEntry->update([
					'baseCurrency' => $request->currency, 
					'compared_to' => $request->compared_to, 
					'threshold'=> $request->threshold
				]);
				return response()->json(['message'=> 'Base currency has been updated successfully'], 200);
			}
			
			$storeCurrency = new \App\UserCurrency();
			$storeCurrency->user_id = $user->id;
			$storeCurrency->baseCurrency = $request->currency;
			$storeCurrency->currentRate = $request->currentRate;
			$storeCurrency->compared_to = $request->compared_to;
			$storeCurrency->threshold = $request->threshold;
			$storeCurrency->save();
			if($storeCurrency->save()){
				return response()->json(['message'=> 'Base currency has been set successfully'], 200);
			}else{
				return response()->json(['message'=> 'Failed to set base currency'], 400);
			}
		}
	}
	
	public function alertNotification(){
		
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
			}else{
				dd($rates);
			}	
			
		}
		
		
	}
}

<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7\Response;

class fixerAPI
{
	protected $url;
	public function __construct(){
		$this->url = env('BASE_FIXER_URL', 'http://data.fixer.io/api/');
	}
	
	public function FixerCurrencies($endpoint){
		try{
			$query =[
				'access_key' => env('ACCESS_KEY')
			];
			$client = new \GuzzleHttp\Client();
			$res = $client->request('GET', $this->url.$endpoint, [
				'query' => $query
			]);
			$response = json_decode($res->getBody()->getContents());
			// dd($response);
			return $response;
			
		}catch (Exception $e) {
            //dd($e->getResponse());
            return $e->getResponse()->getBody();
        }
		
	}
	
    
	public function FixerLatestRates($endpoint, $baseCurrency, array $responseCurrencies){
		try{
			$query =[
				'access_key' => env('ACCESS_KEY'),
				'base' => $baseCurrency,
				'symbols' => implode(',', $responseCurrencies)
			];
			$client = new \GuzzleHttp\Client();
			$res = $client->request('GET', $this->url.$endpoint, [
				'query' => $query
			]);
			$response = json_decode($res->getBody()->getContents());
			return $response;
			
		}catch (Exception $e) {
            //dd($e->getResponse());
            return $e->getResponse()->getBody();
        }
		
	}
	

}

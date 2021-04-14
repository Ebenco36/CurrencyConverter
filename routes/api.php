<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['jwt.verify']], function() {
	// Loan Calculator
	Route::post('generate_repayment', 'API\LoanCalculatorController@index');
	
	// Currency Converter
	Route::post('currency_converter', 'API\CurrencyConverterController@index');
	
	// Currency lists
	Route::get('currencies', 'API\CurrencyConverterController@listCurrencies');
	
	// Set Base Currency 
	Route::post('setBaseCurrency', 'API\CurrencyConverterController@setBaseCurrency');
	
	// TEST
	Route::post('alertNotification', 'API\CurrencyConverterController@alertNotification');
	
	// Current user
	Route::get('user', 'API\Auth\LoginController@user');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'API\Auth\LoginController@login');
	Route::post('register', 'API\Auth\RegisterController@store');
	
});

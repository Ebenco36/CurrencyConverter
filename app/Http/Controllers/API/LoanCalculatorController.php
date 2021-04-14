<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use carbon\Carbon;
class LoanCalculatorController extends Controller
{
    public function index(Request $request){
		$validator = Validator::make($request->all(), [ 
            'amount' => 'required|numeric', 
            'tenure' => 'required|numeric', 
            'repayment_day' => 'required|numeric', 
            'interest' => 'required|numeric', 
        ]);
		if ($validator->fails()) {
            $error = response()->json(['error'=>$validator->errors()], 422);
            return $error;
        }
		$currentDate = date('Y-m-d');
        $currentD = Carbon::createFromFormat('Y-m-d', $currentDate)->format('d');
		$loan_procedures = array();
		for($i=1; $i <= $request->tenure; $i++) {
			$getNextMonth = $this->getNextDay($request->repayment_day, $currentD, $i)->format('Y-m-d');
			$loan_procedures[] = [
                'loan_id'=>$i,
                'repayment_date'=>$getNextMonth,
                'principal'=>floatval($request->amount / $request->tenure),
                'interest'=>floatval($request->amount * ($request->interest / 100)),
                'total' => floatval(($request->amount / $request->tenure) + ($request->amount * ($request->interest / 100))),	
                'payment_status' => 0
            ];
        }
        return response()->json(['status' => true, 'message'=>'Successfully generated', 'procedures' => $loan_procedures]);
		
	}
	
	
	public function getNextDay($num_day, $start_day, $num_months){

		$ddt_month = (int) \carbon\Carbon::now()->month;
        $ddt_year = (int) \carbon\Carbon::now()->year;
        $current = Carbon::now();
		$num_days = cal_days_in_month(CAL_GREGORIAN, $ddt_month, $ddt_year);
        $month_date = $current->addMonths($num_months);
		$repaymentDate = Carbon::create($month_date->format('Y'), $month_date->format('m'), $num_day);
		return $repaymentDate;
    }
}

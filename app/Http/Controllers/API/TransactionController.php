<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Support\Facades\Hash;
use DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $balance = Transaction::select(
        //                         DB::raw('SUM(CASE WHEN TRANSACTIONS.TYPE = 0 THEN TRANSACTIONS.VALUE 
        //                         * -1 ELSE TRANSACTIONS.VALUE END) AS balance')
        //                         )->first();

        // $withdrawal = Transaction::select(
        //                         DB::raw('SUM(CASE WHEN TRANSACTIONS.TYPE = 0 THEN 
        //                                     TRANSACTIONS.VALUE END) AS withdrawal'))->first();
        
        // $deposit = Transaction::select(
        //                         DB::raw('SUM(CASE WHEN TRANSACTIONS.TYPE = 1 THEN 
        //                                     TRANSACTIONS.VALUE END) AS deposit'))->first();

        $current_account = Transaction::select(
            DB::raw('SUM(CASE WHEN (TRANSACTIONS.TYPE IN (0, 2)) AND ACCOUNT_ORIGIN = 1 THEN TRANSACTIONS.VALUE 
                        * -1 ELSE TRANSACTIONS.VALUE END) AS current_account')
                    )->where('account_origin', 1)->orWhere('account_destiny', 1)->first();
        
        $saving_account = Transaction::select(
            DB::raw('SUM(CASE WHEN (TRANSACTIONS.TYPE IN (0, 2)) AND ACCOUNT_ORIGIN = 2 THEN TRANSACTIONS.VALUE 
                        * -1 ELSE TRANSACTIONS.VALUE END) AS saving_account')
                    )->where('account_origin', 2)->orWhere('account_destiny', 2)->first();
        
        $salary_account = Transaction::select(
            DB::raw('SUM(CASE WHEN (TRANSACTIONS.TYPE IN (0, 2)) AND ACCOUNT_ORIGIN = 3 THEN TRANSACTIONS.VALUE 
                        * -1 ELSE TRANSACTIONS.VALUE END) AS salary_account')
                    )->where('account_origin', 3)->orWhere('account_destiny', 3)->first();
        
        $transactions_current_account = DB::table('transactions')->where('account_origin', 1)->paginate(100);
        $transactions_saving_account = DB::table('transactions')->where('account_origin', 2)->paginate(100);
        $transactions_salary_account = DB::table('transactions')->where('account_destiny', '<>', null)->paginate(100);
        
        $transaction = Transaction::latest()->paginate(100);

        // return response()->json($balance, $transaction);

        return [
            "transactions_current_account" => $transactions_current_account,
            "transactions_saving_account" => $transactions_saving_account,
            "transactions_salary_account" => $transactions_salary_account,
            "transaction" => $transaction,
            "current_account" => $current_account->current_account,
            "saving_account" => $saving_account->saving_account,
            "salary_account" => $salary_account->salary_account
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|integer|max:2',
            'value' => 'required|integer|max:999|min:1',
        ]);

        return Transaction::create([
            'type' => $request['type'],
            'value' => $request['value'],
            'account_origin' => $request['accountOrigin'],
            'account_destiny' => $request['accountDestiny']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($account)
    {
        $transactions = DB::table('transactions')
            ->where('account_origin', $account)->orWhere('account_destiny', $account)
            ->latest()->paginate(100);

        return [
            "transacoes" => $transactions,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $transaction = Transaction::find($id);

        $this->validate($request, [
            'type' => 'required|boolean',
            'value' => 'required|integer|max:999|min:1',
        ]);
            
        $transaction->update($request->all());
        
        return ['message' => 'updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
     
        $transaction = Transaction::find($id);
        
        $transaction->delete();

        return ['message' => 'Transaction has been deleted'];
    }
}

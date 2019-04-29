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
        /*
        SELECT SUM(
            CASE WHEN T1.TYPE = 0 THEN T1.VALUE * -1 ELSE T1.VALUE END)
        FROM TRANSACTIONS T1
        */

        // $balance = DB::table('transactions')->sum('(CASE WHEN transactions.type = 0 THEN transactions.value END)');
        //(CASE WHEN transactions.type = 0 THEN transactions.value * -1 ELSE transactions.value END)

        $balance = Transaction::select(DB::raw('SUM(CASE WHEN TRANSACTIONS.TYPE = 0 THEN TRANSACTIONS.VALUE * -1 ELSE TRANSACTIONS.VALUE END) AS balance'))->first();


        $transaction = Transaction::latest()->paginate(10);

        // return response()->json($balance, $transaction);

        return ["transaction" => $transaction, "balance" => $balance];
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
            'type' => 'required|boolean|max:1',
            'value' => 'required|integer|max:999|min:1',
        ]);

        return Transaction::create([
            'type' => $request['type'],
            'value' => $request['value'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

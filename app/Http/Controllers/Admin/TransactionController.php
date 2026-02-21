<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Loantype;
use App\Models\Loantypevalidation;
use App\Models\Referee;
use App\Models\Referrer;
use App\Models\Tol;
use App\Models\tolval;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function index()
    {
         
         $transactions = Referee::orderBy('created_at', 'desc')->get();
         

        return view('admin.transactions.index', compact('transactions'));
    }




    public function create()
    {
       $tolvals = tolval::all();
         $referees = Referee::all();
         $referrers = Referrer::all();
         return view('admin.transactions.create', compact(['referees','referrers','tolvals']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerRequest $request)
    {
            $sms = $request->has('sms') ? '1' : '0';
    $email = $request->has('email') ? '1' : '0';
      $previousState = $request->has('previousState') ? '1' : '0';
    $currentState = $request->has('currentState') ? '2' : '0';
           Transaction::create($request->validated() + ['refereeID' => $request->refereeID,
           'referrerID'=>$request->referrerID,
           'previousState'=>$previousState,
           'currentState'=>$currentState,
           'sms'=>$sms,
           'email'=>$email,
           'comment' => $request->comment,
 
    ]);

        return redirect()->route('admin.transactions.index')->with([
            'message' => 'successfully created !',
            'alert-type' => 'success'
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
               return view('admin.transactions.edit', compact('transaction'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerRequest $request, Transaction $transaction)
{
    // Check if 'sms' and 'email' are set, if not set them to '0'
    $sms = $request->has('sms') ? '1' : '0';
    $email = $request->has('email') ? '1' : '0';

    // Now update the model
    $transaction->update([
       'refereeID' => $request->refereeID,
           'referrerID'=>$request->referrerID,
           'previousState'=>$request->previousState,
           'currentState'=>$request->currentState,
           'sms'=>$sms,
           'email'=>$email,
           'comment' => $request->comment,
    ]);

    return redirect()->route('admin.transactions.index')->with([
        'message' => 'Successfully updated!',
        'alert-type' => 'info'
    ]);
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->back()->with([
            'message' => 'successfully Deleted !',
            'alert-type' => 'danger'
        ]);
    }
    public function massDestroy(Request $request)
    {
        Transaction::whereIn('id', request('ids'))->delete();

        return response()->noContent();

    }
}

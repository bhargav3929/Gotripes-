<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Loantype;
use App\Models\Referee;
use App\Models\Referrer;
use App\Models\tolval;
use App\Models\Transaction;
use Illuminate\Http\Request;

class RefereeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
{
    // Retrieve distinct refereeMobile values
    $refereeMobiles = Referee::distinct('refereeMobile')->pluck('refereeMobile');

    // Retrieve the first record for each unique refereeMobile
    $referees = collect();
    foreach ($refereeMobiles as $mobile) {
        $referee = Referee::where('refereeMobile', $mobile)->orderBy('created_at', 'desc')->first();
        if ($referee) {
            $referees->push($referee);
        }
    }

    return view('admin.referees.index', compact('referees'));
}


    public function create()
    {
        $referrers = Referrer::all();
         $loantypes=Loantype::all();
         return view('admin.referees.create', compact(['loantypes','referrers']));
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
    Referee::create($request->validated() + [
        'refereeName' => $request->refereeName,
        'refereeEmail' => $request->refereeEmail,
        'refereeMobile' => $request->refereeMobile,
        'refereeAddress' => $request->refereeAddress,
        'tolID' => $request->tolID ,
        'referrerID' => $request->referrerID ,
          'previousState'=>$request->previousState,
           'currentState'=>$request->currentState,
        'sms'=>$sms,
           'email'=>$email,
    ]);

    return redirect()->route('admin.referees.index')->with([
        'message' => 'Successfully created!',
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
    public function edit(Referee $referee)
    {
          $tolvals = tolval::all();
        $referrers = Referrer::all();
         $loantypes=Loantype::all();
               return view('admin.referees.edit', compact(['referee','referrers','loantypes','tolvals']));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerRequest $request,Referee $referee)
    {
         $sms = $request->has('sms') ? '1' : '0';
    $email = $request->has('email') ? '1' : '0';
    //     $referee->update($request->validated()+['refereeName' => $request->refereeName,
    // 'refereeEmail' => $request->refereeEmail,
    // 'refereeMobile' => $request->refereeMobile,
    // 'refereeAddress' => $request->refereeAddress,
    // 'tolID' => $request->tolID ,
    // 'referrerID' => $request->referrerID ,
    //   'previousState'=>$request->previousState,
    //        'currentState'=>$request->currentState,
    // 'sms'=>$sms,
    //        'email'=>$email,

    //     ] );
  
        // Create a new transaction record
        Referee::create($request->validated()+[
            'refereeName' => $request->refereeName,
    'refereeEmail' => $request->refereeEmail,
    'refereeMobile' => $request->refereeMobile,
    'refereeAddress' => $request->refereeAddress,
    'tolID' => $request->tolID ,
    'referrerID' => $request->referrerID ,
      'previousState'=>$request->previousState,
           'currentState'=>$request->currentState,
    'sms'=>$sms,
           'email'=>$email,
           'account' => $request->account,
        ]);
        
   
        return redirect()->route('admin.referees.index')->with([
            'message' => 'successfully updated !',
            'alert-type' => 'info'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Referee $referee)
    {
        $referee->delete();

        return redirect()->back()->with([
            'message' => 'successfully Deleted !',
            'alert-type' => 'danger'
        ]);
    }
    public function massDestroy(Request $request)
    {
        Referee::whereIn('id', request('ids'))->delete();

        return response()->noContent();

    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Loantype;
use App\Models\Loantypevalidation;
use App\Models\Tol;
use App\Models\tolval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoanTypeValidationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
         $tolvals = tolval::all();
         

        return view('admin.typeofloanValidations.index', compact('tolvals'));
    }





    public function create()
    {
         return view('admin.typeofloanValidations.create');
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
           tolval::create($request->validated() + ['name' => $request->name,
    'comment' => $request->comment,
    'sms'=>$sms,
    'email'=>$email
 
    ]);

        return redirect()->route('admin.tolvals.index')->with([
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
    public function edit(tolval $tolval)
    {
               return view('admin.typeofloanValidations.edit', compact('tolval'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerRequest $request, tolval $tolval)
{
    // Check if 'sms' and 'email' are set, if not set them to '0'
    $sms = $request->has('sms') ? '1' : '0';
    $email = $request->has('email') ? '1' : '0';

    // Now update the model
    $tolval->update([
        'name' => $request->name,
        'comment' => $request->comment,
        'sms' => $sms,
        'email' => $email
    ]);

    return redirect()->route('admin.tolvals.index')->with([
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
    public function destroy(tolval $tolval)
    {
        $tolval->delete();

        return redirect()->back()->with([
            'message' => 'successfully Deleted !',
            'alert-type' => 'danger'
        ]);
    }
    public function massDestroy(Request $request)
    {
        tolval::whereIn('id', request('ids'))->delete();

        return response()->noContent();

    }
}

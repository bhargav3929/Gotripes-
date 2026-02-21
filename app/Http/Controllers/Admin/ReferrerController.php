<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Referrer;
use Illuminate\Http\Request;

class ReferrerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
         $referrers = Referrer::all();
         

        return view('admin.referrers.index', compact('referrers'));
    }





    public function create()
    {
         return view('admin.referrers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(BannerRequest $request)
{
    // Check if a referrer with the same mobile number already exists
    $existingReferrer = Referrer::where('referrerMobile', $request->referrerMobile)->first();
    if ($existingReferrer) {
        return redirect()->back()->withInput()->with([
            'message' => 'This Referrer Details are already exist, Please enter new Referrer',
            'alert-type' => 'danger'
        ]);
    }

    // If not, create the new referrer
    Referrer::create($request->validated() + [
        'referrerName' => $request->referrerName,
        'referrerEmail' => $request->referrerEmail,
        'referrerMobile' => $request->referrerMobile,
    ]);

    return redirect()->route('admin.referrers.index')->with([
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
    public function edit(Referrer $referrer)
    {
               return view('admin.referrers.edit', compact('referrer'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerRequest $request,Referrer $referrer)
    {
        $referrer->update($request->validated()+['referrerName' => $request->referrerName,
    'referrerEmail' => $request->referrerEmail,
    'referrerMobile' => $request->referrerMobile,

        ] );

        return redirect()->route('admin.referrers.index')->with([
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
    public function destroy(Referrer $referrer)
    {
        $referrer->delete();

        return redirect()->back()->with([
            'message' => 'successfully Deleted !',
            'alert-type' => 'danger'
        ]);
    }
    public function massDestroy(Request $request)
    {
        Referrer::whereIn('id', request('ids'))->delete();

        return response()->noContent();

    }
}

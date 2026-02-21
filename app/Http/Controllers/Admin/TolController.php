<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Loantype;
use App\Models\Tol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
         $tols = Loantype::all();
         

        return view('admin.typeofloans.index', compact('tols'));
    }





    public function create()
    {
         return view('admin.typeofloans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerRequest $request)
    {
           Loantype::create($request->validated() + ['tolName' => $request->tolName,
    'comment' => $request->comment,
 
    ]);

        return redirect()->route('admin.tols.index')->with([
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
    public function edit(Loantype $tol)
    {
               return view('admin.typeofloans.edit', compact('tol'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerRequest $request,Loantype $tol)
    {
        $tol->update($request->validated()+['tolName' => $request->tolName,
    'comment' => $request->comment,

        ] );

        return redirect()->route('admin.typeofloans.index')->with([
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
    public function destroy(Loantype $tol)
    {
        $tol->delete();

        return redirect()->back()->with([
            'message' => 'successfully Deleted !',
            'alert-type' => 'danger'
        ]);
    }
    public function massDestroy(Request $request)
    {
        Loantype::whereIn('id', request('ids'))->delete();

        return response()->noContent();

    }
}

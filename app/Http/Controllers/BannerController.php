<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerMail;
use App\Models\Banner;
use App\Models\Loantype;
use App\Models\Referrer;

class BannerController extends Controller
{

public function BannerSetup() 
{
    $banners = Banner::all();
 $referrers = Referrer::all();
     $loantypes=Loantype::all();
    return view('welcome', compact(['banners','referrers','loantypes']));
}


}
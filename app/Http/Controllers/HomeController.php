<?php

namespace App\Http\Controllers;

use App\Models\RabbitBreeder;
use App\Models\RabbitKit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use QrReader;

use App\Helpers\BaseIntEncoder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth',['only' => ['index']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cache = false;
        $user = auth()->user();
        return view('app', ['user' => $user]);
    }

    public function external(Request $request){
        if($breeder = RabbitBreeder::find(BaseIntEncoder::decode($request->id))){
            $pedigree = $breeder->pedigree();
            $owner = $breeder->user;
            return view('layouts.profile.public')->with(compact('pedigree', 'owner'));
        }else{
            return view('layouts.profile.public_not_found');
        }
    }

    public function externalKit(Request $request){
        if($kit = RabbitKit::find(BaseIntEncoder::decode($request->id))){
            $pedigree = $kit->pedigree();
            $owner = $kit->user;
            return view('layouts.profile.public')->with(compact('pedigree', 'owner'));
        }else{
            return view('layouts.profile.public_not_found');
        }
    }

    public function externalRedirect($id){
        return redirect(route('web.pedigree', ['id' => $id]));
    }

    public function redirect(Request $request)
    {
        $host = 'htch.us';
        if (($port = $request->getPort()) && $port != "80") {
            $host .= ':' . $port;
        }
        return redirect('//' . $host . $request->getRequestUri());
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
    }

    public function invite(User $inviter, Request $request)
    {
        // Save inviting user, then redirect to the registration page
        $request->session()->set('inviter', $inviter->id);
        return redirect('/register');
    }

    public function qrcodeupload(Request $request)
    {
        $image = $request->file('file');
        $imageName = time().$image->getClientOriginalName();
        $image->move(public_path('images'),$imageName);
        return response()->json(['success'=>$imageName]);
    }

    public function qrcodescan(Request $request)
    {
        $image_url = $_GET['image_url'];
        $qrcode = new QrReader($image_url);
        $product_url = $qrcode->text();

        if ($product_url != '') {
            return response()->json([
                'success'=>'true',
                'product_url'=>$product_url
            ]);    
        } else {
            return response()->json(['success'=>'false']);
        }
    }

}

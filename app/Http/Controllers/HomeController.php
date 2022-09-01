<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Connections;
use App\Models\User;
use App\Services\ConnectionService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {   
    
          
        $type= $request->query('type')?$request->query('type'):'suggestions';
        $user = auth()->user();

        $sendRequests =      
            
        $receivedRequests = ConnectionService::receivedRequests($user); 
       
        $connections = ConnectionService::connections($user); 
        $connections = ConnectionService::getCommonConnection($connections,$user);
        $suggestions = ConnectionService::suggestions($user);

       
        
        return view('home',compact("sendRequests","receivedRequests","connections","suggestions","type"));
    }
}

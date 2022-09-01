<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Connections;
use App\Models\User;
use App\Services\ConnectionService;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request);
        $type = $request->query('type') ? $request->query('type') : 'suggestions';
        $user = auth()->user();
      
        $userId = $request->query('user_id') ? $request->query('user_id') : 0;
        if ($type == 'sent') {
            $data = ConnectionService::sendRequests($user); 
            return $data;
        }
        if ($type == 'received') {
            $data =  ConnectionService::connections($user); 
            return $data;
        }
        if ($type == 'connections') {
            $connections = ConnectionService::connections($user); 
            $connections = ConnectionService::getCommonConnection($connections,$user);
            return response()->json($connections);
        }
        if ($type == 'suggestions') {
       
            $data = ConnectionService::suggestions($user);
            return $data;
        }
        if ($type == 'common-connections') {


            $data = ConnectionService::commonForAjax($user,$userId);
          
            return $data;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        dd($user);
        // Connections::create(['']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $receiverId = $request->input('id');
        $createRequest = Connections::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'status' => 1
        ]);
        return redirect()->route('home');
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
    public function edit($id)
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
        Connections::where('id', $id)->update(['status' => 2]);
        return "updated";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Connections::where('id', $id)->delete();
        return "deleted";
    }
}

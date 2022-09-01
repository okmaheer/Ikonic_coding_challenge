<?php

namespace App\Services;

use App\Models\Connections;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConnectionService
{
    public static function sendRequests($user){
           
        return Connections::where('sender_id',$user->id)->with('receiver')->where('status',1)->Paginate(10);
    }

    

    public static function receivedRequests($user){
           
        return Connections::where('receiver_id',$user->id)->with('sender')->where('status',1)->Paginate(10);
    }
    public static function connections($user){
        
    
        return Connections::where('status',2)->where(function($q) use($user) { 
            $q->where('receiver_id',$user->id)
            ->orWhere('sender_id',$user->id);})->with(['sender','receiver'])->Paginate(10);
    }

    public static function getCommonConnection($connections,$user){

        $connectedUsers = Connections::where('status',2)->where(function($q) use($user) {
            $q->where('receiver_id',$user->id)
            ->orWhere('sender_id',$user->id);})->get();
        $connectedUserIds= [];
       
        foreach($connectedUsers as $connectedUser){
            if($connectedUser->receiver_id != $user->id){
                array_push($connectedUserIds,$connectedUser->receiver_id);
            }
            if($connectedUser->sender_id != $user->id){
                array_push($connectedUserIds,$connectedUser->sender_id);
            }
        }
        foreach($connections as $connection){
            $connected_ids=[];
            $userConnections=[];
            if($connection->receiver_id != $user->id){
                $userConnections=Connections::where('sender_id','!=',$user->id)->where('receiver_id','!=',$user->id)->where(function($q) use($connection) {
                    $q->where('receiver_id',$connection->receiver_id)
                    ->orWhere('sender_id',$connection->receiver_id);})->where('status',2)->get();
            }
            if($connection->sender_id != $user->id){
                $userConnections=Connections::where('sender_id','!=',$user->id)->where('receiver_id','!=',$user->id)->where(function($q) use($connection) {
                    $q->where('receiver_id',$connection->sender_id)
                    ->orWhere('sender_id',$connection->sender_id);})->where('status',2)->get();
            }
            foreach($userConnections as $userConnection){
                if($userConnection->receiver_id != $user->id && ($userConnection->receiver_id != $connection->receiver_id && $userConnection->receiver_id != $connection->sender_id)){
                    array_push($connected_ids,$userConnection->receiver_id);
                }
                if($userConnection->sender_id != $user->id && ($userConnection->sender_id != $connection->sender_id && $userConnection->sender_id != $connection->receiver_id)){
                    array_push($connected_ids,$userConnection->sender_id);
                }
            }
            
            $commonConnectionIds=array_intersect($connectedUserIds,$connected_ids);
            $connection->commonConnections = User::whereIn('id', $commonConnectionIds)->where('id','!=',$user->id)->Paginate(10);
        }

        return $connections;

    }

    public static function suggestions($user){
        $allConnections = Connections::where('receiver_id',$user->id)->orWhere('sender_id',$user->id)->get();
        $networkUserIds=[];

        foreach($allConnections as $connection){
            if($connection->receiver_id != $user->id){
                array_push($networkUserIds,$connection->receiver_id);
            }
            if($connection->sender_id != $user->id){
                array_push($networkUserIds,$connection->sender_id);
            }
        }
      
        return User::whereNotIn('id', $networkUserIds)->where('id','!=',$user->id)->Paginate(10);
    }

    public static function commonForAjax($user,$userId){
        $connectedUsers = Connections::where(function ($q) use ($user) {
            $q->where('receiver_id', $user->id)
                ->orWhere('sender_id', $user->id);
        })->where('status', 2)->get();
        $connectedUserIds = [];
        foreach ($connectedUsers as $connectedUser) {
            if ($connectedUser->receiver_id != $user->id) {
                array_push($connectedUserIds, $connectedUser->receiver_id);
            }
            if ($connectedUser->sender_id != $user->id) {
                array_push($connectedUserIds, $connectedUser->sender_id);
            }
        }
        $connected_ids = [];
        $userConnections = Connections::where('sender_id', '!=', $user->id)->where('receiver_id', '!=', $user->id)->where(function ($q) use ($userId) {
            $q->where('receiver_id', $userId)
                ->orWhere('sender_id', $userId);
        })->where('status', 2)->get();
        foreach ($userConnections as $userConnection) {
            if ($userConnection->receiver_id != $user->id && $userConnection->receiver_id != $userId) {
                array_push($connected_ids, $userConnection->receiver_id);
            }
            if ($userConnection->sender_id != $user->id && $userConnection->sender_id != $userId) {
                array_push($connected_ids, $userConnection->sender_id);
            }
        }

        $commonConnectionIds = array_intersect($connectedUserIds, $connected_ids);
        $data = User::whereIn('id', $commonConnectionIds)->where('id', '!=', $user->id)->Paginate(10);

        return $data;
    }

}

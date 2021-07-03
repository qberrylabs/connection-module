<?php

namespace Modules\ConnectionModule\Http\Controllers;

use Modules\ConnectionModule\Events\ConnectionEvent;
use Modules\CoreModule\Http\Controllers\User\UserSingleton;
use Modules\CoreModule\Traits\UserTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ConnectionModule\Entities\Connection;
use Modules\ConnectionModule\Traits\ConnectionTrait;

class ConnectionController extends Controller
{
    use UserTrait , ConnectionTrait;

    private function getUserPenddingConnections()
    {
        $connections=$this->getUserConnectionsByStatus(1);
        return $connections;
    }

    public function getUserConnections($type)
    {

        $connections=[];
        switch ($type) {
            case 'pending':
                $connections=$this->getUserPenddingConnections();
                break;
            case 'all':
                $connections=$this->userAllConnections();
                break;
            default:
                $connections=[];
                break;
        }

        $userConnections=$this->getUserAvailableConnections();

        return view("connectionmodule::connection.index",['connections'=>$connections,'userConnections'=>$userConnections,'type'=>$type]);
    }



    public function create(Request $request)
    {
        $this->validate($request, [
            'with_user_id' => 'required | numeric | exists:users,id',
        ]);
        /*Users Information*/
        $user=UserSingleton::getUser();
        $toUser=$this->getUserInfomationByID($request['with_user_id']);

        $connection=new Connection();
        $connection->user_id=$user->id;
        $connection->with_user_id=$toUser->id;
        $connection->save();

        event(new ConnectionEvent('connection request',$connection,$toUser));

        return back()->with('success','Connection created successfully');

    }

    public function connectionChangeStatus($connectionID,$status)
    {
        $connection=Connection::find($connectionID);

        if (!$connection) {
            return back()->with('failed','Connection Failed');
        }

        if ($status == "cancel") {
            $connection->delete();
            return back()->with('success','Connection cancelled successfully');
        }

        $statusNumber=$this->getConnectionStatusNumber($status);

        $connection->connection_status_id=$statusNumber;
        $connection->save();

        $user=$this->getUserInfomationByID($connection->user_id);
        //$withUser=$this->getUserInfomationByID($connection->with_user_id);

        event(new ConnectionEvent($this->getConnectionTemplateNameByStatusNumber($statusNumber),$connection,$user));

        return back()->with('success','Connection Change  successfully');
    }

    public function getConnections()
    {
        $connections=Connection::with(
            [
                'getUserInformaionByFromConnection:id,full_name,first_name,last_name',
                'getUserInformaionByWithConnection:id,full_name,first_name,last_name',
                'getConnectionStatus:id,connection_status_name'
            ])
            ->orderBy('id','DESC')->get();

        return view('connectionmodule::admin.connections.index',compact('connections'));

    }
}

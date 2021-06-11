<?php

namespace Modules\ConnectionModule\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ConnectionModule\Entities\Connection;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConnectionRequestEmail;
use App\Traits\ErrorHandlingTraits;
use App\Traits\NotificationTraits;
use Modules\CoreModule\Entities\NotificationTemplate;

class ConnectionController extends Controller
{
    use ErrorHandlingTraits , NotificationTraits;

    public function search($full_name)
    {

        $availableConnections = User::whereHas('roles', function ($q) {
            $q->where('name', 'Customer');
        })
        ->where('first_name', 'like', '%' . $full_name . '%')
        ->orWhere('middle_name', 'like', '%' . $full_name . '%')
        ->orWhere('last_name', 'like', '%' . $full_name . '%')
        ->where('is_active', 1)
        ->where('id', '!=',Auth::id())
        ->get();

        return response()->json(['availableConnections' => $availableConnections], 200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'with_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 'false','status'=>$this->getStatusCode(400),'error'=>"The with user id field is required."], 400);
        }
        $userfromInfo=User::find(Auth::id());

        $connection = new Connection();
        $connection->user_id =$userfromInfo->id;
        $connection->with_user_id = $request['with_user_id'];
        $connection->connection_date = now();
        $connection->save();

        $userWithInfo=User::find($request['with_user_id']);

        $data = ['status'=>'request','user_full_name' => $userfromInfo->full_name];
        Mail::to($userWithInfo->email)->send(new ConnectionRequestEmail($data));

        $token=$userWithInfo->device_token;

        $notificationTemplate=NotificationTemplate::where('type','connection request')->first();
        $title=$notificationTemplate->title;
        $content=$notificationTemplate->content;
        $type=$notificationTemplate->type;

        $old = ["user_full_name"];
        $new   = [$userfromInfo->full_name];
        $resContent = str_replace($old, $new,$content);

        $this->sendNotification($userWithInfo,$token,$title,$resContent,$type);




        return response()->json(['message' => 'Connection Successfully Created'], 200);
    }


    public function connectionAccepted($id)
    {
        $connection = Connection::find($id);
        $connection->connection_status_id = 2;
        $connection->save();

        $userWithInfo=User::find($connection->with_user_id);
        $userfromInfo=User::find($connection->user_id);

        $data = ['status'=>'approved','user_full_name' => $userWithInfo->full_name];
        Mail::to($userfromInfo->email)->send(new ConnectionRequestEmail($data));

        $token=$userfromInfo->device_token;

        $notificationTemplate=NotificationTemplate::where('type','connection approved')->first();
        $title=$notificationTemplate->title;
        $content=$notificationTemplate->content;
        $type=$notificationTemplate->type;

        $old = ["user_full_name"];
        $new   = [$userWithInfo->full_name];
        $resContent = str_replace($old, $new,$content);

        $this->sendNotification($userfromInfo,$token,$title,$resContent,$type);



        return response()->json(['message' => 'Connection Successfully Accepted'], 200);

    }


    public function connectionRejected($id)
    {
        $connection = Connection::find($id);
        $connection->connection_status_id = 3;
        $connection->save();

        $userWithInfo=User::find($connection->with_user_id);
        $userfromInfo=User::find($connection->user_id);

        $data = ['status'=>'decline','user_full_name' => $userWithInfo->full_name];
        Mail::to($userfromInfo->email)->send(new ConnectionRequestEmail($data));

        $token=$userfromInfo->device_token;

        $notificationTemplate=NotificationTemplate::where('type','connection decline')->first();
        $title=$notificationTemplate->title;
        $content=$notificationTemplate->content;
        $type=$notificationTemplate->type;

        $old = ["user_full_name"];
        $new   = [$userWithInfo->full_name];
        $resContent = str_replace($old, $new,$content);

        $this->sendNotification($userfromInfo,$token,$title,$resContent,$type);


        return response()->json(['message' => 'Connection Successfully Rejected'], 200);
    }


    public function destroy($id)
    {
        Connection::find($id)->delete();
        return response()->json(['message' => 'Connection Successfully Deleted',], 200);
    }



    public function getUserFullName($id)
    {
        return User::find($id)->full_name;
    }

    public function getUserImg($id)
    {
        return User::find($id)->user_img;
    }

    public function getUserConnections()
    {
        $userID = Auth::id();
        // $connections = Connection::where('user_id', $userID)->orWhere('with_user_id', $userID)->get();

        $connections= Connection::with(
                    ['getConnectionStatus:id,connection_status_name',
                    'getUserInformaionByConnectionFrom:id,name,full_name,user_img',
                    'getUserInformaionByConnectionWith:id,name,full_name,user_img',
                    ]

                )->where(function ($query) {
                    $query->where('user_id', Auth::id());
                    $query->orWhere('with_user_id', Auth::id());
                })->Where(function ($query) {
                    $query->where('connection_status_id', '!=',3);
                })->get();

        return response()->json(['connections' => $connections], 200);
    }


}

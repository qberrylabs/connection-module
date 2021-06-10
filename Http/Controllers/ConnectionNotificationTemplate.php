<?php

namespace Modules\ConnectionModule\Http\Controllers;

use Modules\CoreModule\Entities\NotificationTemplate;
use Modules\CoreModule\Traits\UserTrait;
use Modules\CoreModule\Entities\Notification;
use Exception;
use Illuminate\Support\Facades\Session;

class ConnectionNotificationTemplate
{
    use UserTrait;

    public $type;
    public $user;
    public $connection;

    /* Connection Information */
    public $userID;
    public $withUserID;

    /*Start User Information*/
    public $fromUser;
    public $withUser;

    /* Notification Template */
    public $notificationTemplate;

    public function __construct($type,$user,$connection)
    {
        $this->type=$type;
        $this->user=$user;
        $this->connection=$connection;
        $this->setConnectionVariable();
        $this->setConnectionNotificationTemplate();
    }

    public function setConnectionVariable()
    {
        $this->userID=$this->connection->user_id;
        $this->withUserID=$this->connection->with_user_id;

        $this->fromUser=$this->getUserInfomationByID($this->userID);
        $this->withUser=$this->getUserInfomationByID($this->withUserID);

    }

    public function setConnectionNotificationTemplate()
    {

        $this->notificationTemplate=NotificationTemplate::where('type',$this->type)->first();
    }



    public function getConnectionNotificationTemplate()
    {
        $content=null;
        switch ($this->type) {
            case "connection request":
                $content=$this->getConnectionRequestNotificationTemplate();
                break;
            case "connection approved":
                $content=$this->getConnectionApprovedNotificationTemplate();
                break;
            case "connection decline":
                $content=$this->getConnectionDeclineNotificationTemplate();
                break;

            default:
                Session::flash('failed', 'The Notification Has Not Sent');
                break;
        }
        return $content;
    }


    public function getConnectionRequestNotificationTemplate()
    {
        $old = ['user_full_name'];
        $new   = [$this->fromUser->full_name];
        $content = str_replace($old, $new, $this->notificationTemplate->content);
        return $content;
    }

    public function getConnectionApprovedNotificationTemplate()
    {
        $old = ['user_full_name'];
        $new   = [$this->withUser->full_name];
        $content = str_replace($old, $new, $this->notificationTemplate->content);
        return $content;
    }

    public function getConnectionDeclineNotificationTemplate()
    {
        $old = ['user_full_name'];
        $new   = [$this->withUser->full_name];
        $content = str_replace($old, $new, $this->notificationTemplate->content);
        return $content;
    }


    public function sendNotification()
    {
        $user=$this->user;

        $title=$this->notificationTemplate->title;
        $body=$this->getConnectionNotificationTemplate();
        $type=$this->type;

        $notification=new Notification();
        $notification->user_id=$user->id;
        $notification->notification_type=$type;
        $notification->notification_title=$title;
        $notification->contant=$body;
        $notification->save();

        $userTokens=$user->getUserDeviceTokens()->get();

        foreach ($userTokens as $userToken) {

           $this->sendNotificationToTokens($userToken->device_token,$title,$body,$type);
        }
    }

    public function sendNotificationToTokens($token,$title,$body,$type)
    {
        $firebaseToken = $token;
        $SERVER_API_KEY = env('FIREBASE_SERVER_API_KEY');
        //$SERVER_API_KEY = 'AAAAXF-71xs:APA91bH1ZH77AZiXqD5yiMqtCr6X9yv8d5zg_6CVIxRfIT-IUJ2S8fXtqhefpkIyfZ0emVvHVZZ_IPMF8fxl0JRMddK11I-4I5PJQt7yLSJdjFmXe0ItwsxJ6JiYWhoTDH1-el5n2zni';

        $data = [
            "registration_ids" =>array($firebaseToken),
            "notification" => [
                "title" => $title,
                "body" => $body,
                "type" =>$type
            ]
        ];
        $dataString = json_encode($data);
        //dd($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
            //Session::flash('failed', 'The Notification Has Not Sent');
        }


    }

}

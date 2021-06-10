<?php

namespace Modules\ConnectionModule\Traits;

use App\Enum\Settings;
use Illuminate\Http\Request;
use Modules\CoreModule\Http\Controllers\User\UserSingleton;
use App\Models\User;

use Modules\CoreModule\Traits\UserTrait;
use Modules\CoreModule\Traits\WalletTrait;
use Illuminate\Support\Facades\Session;
use Modules\ConnectionModule\Entities\Connection;
use Modules\CoreModule\Entities\Wallet;

trait ConnectionTrait {

    use WalletTrait , UserTrait;

    public function getConnectionTemplateNameByStatusNumber($statusNumber)
    {
        $templateName=null;
        switch ($statusNumber) {
            case 2:
                $templateName='connection approved';
                break;
            case 3:
                $templateName='connection decline';
                break;
            default:
                Session::flash('failed', 'Connection Template Name By Status Number');
                break;
        }
        return $templateName;
    }

    public function getConnectionStatusNumber($status)
    {
        $statusNumber=null;

        switch ($status) {
            case 'accept':
                $statusNumber=2;
                break;

            case 'decline':
                $statusNumber=3;
                break;

            default:
                Session::flash('failed', 'Connection Failed');
                break;
        }
        return $statusNumber;
    }

    private function getUserFullName($id)
    {
        return User::find($id)->name;
    }

    private function getConnectionShowUser($connection)
    {
        $userID = UserSingleton::getUser()->id;

        if ($connection->user_id == $userID) {
            return $this->getUserFullName($connection->with_user_id);

        }elseif($connection->with_user_id == $userID){
            return $this->getUserFullName($connection->user_id);
        }else{
            return " ";
        }
    }

    private function getConnectionAction($connection)
    {
        $userID = UserSingleton::getUser()->id;

        if ($connection->user_id == $userID) {
            return "Sender";

        }elseif($connection->with_user_id == $userID){
            return "Received";
        }else{
            return " ";
        }
    }

    public function getUserConnectionCountByID($id)
    {
        $userID = UserSingleton::getUser()->id;

        $connectionsCounts = Connection::where(function ($query) use ($userID )  {
            $query->where('user_id', $userID);
            $query->orWhere('with_user_id', $userID);
        })->Where(function ($query)  use ($id){
            $query->where('connection_status_id',$id);

        })->count();

        return $connectionsCounts;

    }

    public function userAllConnections()
    {
        $userID = UserSingleton::getUser()->id;

        $connections = Connection::with('getConnectionStatus')->where(function ($query) use ($userID )  {
            $query->where('user_id', $userID);
            $query->orWhere('with_user_id', $userID);
        })->get();

        foreach ($connections as $connection) {
            $connection->setAttribute('user_name', $this->getConnectionShowUser($connection));
            $connection->setAttribute('connection_action', $this->getConnectionAction($connection));
        }

        return  $connections;
    }

    public function getUserWalletConnection($connection)
    {
        $userID = UserSingleton::getUser()->id;
        $wallet=null;

        if ($connection->user_id == $userID) {
            $wallet= $this->getWalletByUserID($connection->with_user_id);

        }elseif($connection->with_user_id == $userID){
            return $this->getWalletByUserID($connection->user_id);
        }else{
            return back();
        }
        return $wallet;
    }

    public function getUserConnectionsByStatus($id)
    {
        $userID = UserSingleton::getUser()->id;
        //$connections = Connection::where('user_id', $userID)->Where('connection_status_id',$id)->get();

        $connections = Connection::with('getConnectionStatus')->where(function ($query) use ($userID )  {
            $query->where('user_id', $userID);
            $query->orWhere('with_user_id', $userID);
        })->Where(function ($query)  use ($id){
            $query->where('connection_status_id',$id);
        })->get();

        foreach ($connections as $connection) {

            $connection->setAttribute('user_name', $this->getConnectionShowUser($connection));
            $connection->setAttribute('connection_action', $this->getConnectionAction($connection));
            $connection->setAttribute('wallet', $this->getUserWalletConnection($connection));
        }

        return  $connections;
    }

    public function checkAvailableConnections($withUserID) :bool
    {
        $userID=UserSingleton::getUser()->id;

        $connection = Connection::where(function ($query) use ($userID ,$withUserID)  {
            $query->where('user_id', $userID);
            $query->Where('with_user_id', $withUserID);

        })->orWhere(function ($query) use ($userID ,$withUserID){
            $query->where('with_user_id', $userID);
            $query->Where('user_id', $withUserID);
        })->exists();

        if ($connection) {
            return true;
        }else{
            return false;
        }

    }

    public function getUserAvailableConnections()
    {
        $user=UserSingleton::getUser();
        $connections= Wallet::where('user_id','!=',$user->id)->with(['getUserInformations','userWallet'])->get();

        foreach ($connections as $connection) {
            $user=$connection->getUserInformations;
            $userID=$user->id;
            $checkAvailableConnections=$this->checkAvailableConnections($userID);
            if (!$checkAvailableConnections) {
                //$user=$this->getUserInfomationByID($userID);
                $connection->setAttribute('user', $user);
            }
        }
        return $connections;
    }
}

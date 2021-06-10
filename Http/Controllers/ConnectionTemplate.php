<?php

namespace App\Http\Controllers\Connection;

use Modules\CoreModule\Entities\Template;
use Modules\CoreModule\Traits\UserTrait;
use Illuminate\Support\Facades\Session;

class ConnectionTemplate
{
    use UserTrait;

    public $type;
    public $connection;

    /*Start Connection Information*/
    public $userID;
    public $withUserID;
    /*End Connection Information*/

    /*Start User Information*/
    public $user;
    public $withUser;
    /*End User Information*/

    /*Start Template Information */
    public $emailTemplate;
    /*End Template Information */

    public function __construct($type,$connection)
    {
        $this->type=$type;
        $this->connection=$connection;
        $this->setConnectionVariable();
        $this->setConnectionEmailTemplate();
    }

    public function setConnectionVariable()
    {
        $this->userID=$this->connection->user_id;
        $this->withUserID=$this->connection->with_user_id;

        $this->user=$this->getUserInfomationByID($this->userID);
        $this->withUser=$this->getUserInfomationByID($this->withUserID);

    }

    public function setConnectionEmailTemplate()
    {
       $this->emailTemplate=Template::where('name',$this->type)->first();
    }



    public function getConnectionEmailTemplate()
    {
        $content=null;
        switch ($this->type) {
            case "connection request":
                $content=$this->getConnectionRequestEmailTemplate();
                break;
            case "connection approved":
                $content=$this->getConnectionApprovedEmailTemplate();
                break;
            case "connection decline":
                $content=$this->getConnectionDeclineEmailTemplate();
                break;

            default:
                Session::flash('failed', 'The Email Has Not Sent');
                break;
        }
        return $content;
    }


    public function getConnectionRequestEmailTemplate()
    {
        $old = ['user_full_name'];
        $new   = [$this->user->full_name];
        $content = str_replace($old, $new, $this->emailTemplate->content);
        return $content;
    }

    public function getConnectionApprovedEmailTemplate()
    {
        $old = ['user_full_name'];
        $new   = [$this->withUser->full_name];
        $content = str_replace($old, $new, $this->emailTemplate->content);
        return $content;
    }

    public function getConnectionDeclineEmailTemplate()
    {
        $old = ['user_full_name'];
        $new   = [$this->withUser->full_name];
        $content = str_replace($old, $new, $this->emailTemplate->content);
        return $content;
    }


}

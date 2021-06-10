<?php

namespace Modules\ConnectionModule\Listeners;

use Modules\CoreModule\Traits\UserTrait;
use Modules\ConnectionModule\Events\ConnectionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Modules\ConnectionModule\Emails\SendConnectionMail;
use Modules\ConnectionModule\Http\Controllers\ConnectionNotificationTemplate;

class SendConnectionAlert
{
    use UserTrait;
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ConnectionEvent  $event
     * @return void
     */
    public function handle(ConnectionEvent $event)
    {
        $type=$event->type;
        $connection=$event->connection;
        $toUser=$event->toUser;

        try {
            Mail::to($toUser)->send(new SendConnectionMail($type,$connection));
        } catch (\Throwable $th) {
            Session::flash('failed', 'The Email Has Not Sent');

        }
        try {
            $connectionNotificationTemplate=new ConnectionNotificationTemplate($type,$toUser,$connection);
            $connectionNotificationTemplate->sendNotification();
        } catch (\Throwable $th) {
            Session::flash('failed', 'The Notification Has Not Sent');
        }
    }
}

<?php

namespace Modules\ConnectionModule\Emails;

use App\Http\Controllers\Connection\ConnectionTemplate;
use Modules\CoreModule\Entities\Template;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConnectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $connection;

    public function __construct($type,$connection)
    {
        $this->type=$type;
        $this->connection=$connection;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            $type=$this->type;
            $emailTemplate=Template::where('name',$type)->first();
            $connectionEmailTemplate=new ConnectionTemplate($type,$this->connection);
            $content=$connectionEmailTemplate->getConnectionEmailTemplate();

            $address = env('MAIL_FROM_ADDRESS');
            $subject = $emailTemplate->subject;
            $name = env('MAIL_FROM_NAME');
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }

        return $this->view('emails.send_mail')
                    ->from($address, $name)
                    ->cc($address, $name)
                    ->bcc($address, $name)
                    ->replyTo($address, $name)
                    ->subject($subject)
                    ->with(['content' => $content] );
    }
}

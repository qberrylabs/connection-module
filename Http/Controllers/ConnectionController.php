<?php

namespace Modules\ConnectionModule\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ConnectionModule\Entities\Connection;

class ConnectionController extends Controller
{

    public function getConnections()
    {
        $connections=Connection::with(
            [
                'getUserInformaionByFromConnection:id,full_name',
                'getUserInformaionByWithConnection:id,full_name',
                'getConnectionStatus:id,connection_status_name'
            ])
            ->orderBy('id','DESC')->get();

        return view('connectionmodule::admin.connections.index',compact('connections'));

    }
}

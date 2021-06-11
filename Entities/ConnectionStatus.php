<?php

namespace Modules\ConnectionModule\Entities;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;

class ConnectionStatus extends Model
{
    use ClearsResponseCache;
}

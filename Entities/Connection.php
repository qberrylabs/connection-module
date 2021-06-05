<?php

namespace Modules\ConnectionModule\Entities;

use App\Models\ConnectionStatus;
use App\Models\User;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use ClearsResponseCache;
    protected $fillable = [
        'user_id', 'connection_status_id', 'with_user_id','connection_date'
    ];

    public function getConnectionStatus()
    {
        return $this->belongsTo(ConnectionStatus::class,'connection_status_id');
    }
    public function getUserInformaionByFromConnection()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function getUserInformaionByWithConnection()
    {
        return $this->belongsTo(User::class,'with_user_id');
    }
}

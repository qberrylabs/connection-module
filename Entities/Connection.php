<?php

namespace Modules\ConnectionModule\Entities;


use App\Models\User;
use App\Traits\ClearsResponseCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use ClearsResponseCache;
    protected $fillable = [
        'user_id', 'connection_status_id', 'with_user_id','connection_date','created_at'
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



    public function getUserInformaionByConnectionFrom()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function getUserInformaionByConnectionWith()
    {
        return $this->belongsTo(User::class,'with_user_id');
    }

    public function getCreatedAtAttribute($value)
    {
        //dd($value);
        return Carbon::parse($value)->diffForHumans();
    }



}

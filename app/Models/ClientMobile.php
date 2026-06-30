<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMobile extends Model
{
    protected $fillable = ['client_id','name', 'country', 'mobile'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

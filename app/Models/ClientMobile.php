<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMobile extends Model
{
    protected $fillable = ['client_id','name', 'country', 'mobile','notify'];
    protected $casts    = ['notify' => 'boolean'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

        public function mobiles()
    {
        return $this->hasMany(ClientMobile::class, 'client_id');
    }
}


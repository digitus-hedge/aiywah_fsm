<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
class ServiceType extends Model
{
    use LogsActivity;

     protected $fillable = [
        'category_id',
        'domain',

    ];
}
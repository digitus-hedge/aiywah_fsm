<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;
class Warranty extends Model
{
   use LogsActivity;
    use SoftDeletes;

    protected $fillable = ['name', 'value', 'status'];

    protected $casts = ['status' => 'boolean'];
}

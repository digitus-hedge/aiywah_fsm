<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;
class Client extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'unique_code',
        'contact_name',
        'designation',
        'primary_country',
        'primary_mobile',
        'email',
    ];

    public function mobiles()
    {
        return $this->hasMany(ClientMobile::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasUuids;
    protected $fillable = [
        'full_name',
        'username',
        'password',
        'access_token',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class,);
    }
}

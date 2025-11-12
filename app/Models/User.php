<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'full_name',
        'username',
        'password',
        'access_token',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}

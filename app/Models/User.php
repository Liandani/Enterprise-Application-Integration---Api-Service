<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone'
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function loanHistories()
    {
        return $this->hasMany(LoanHistory::class);
    }
}

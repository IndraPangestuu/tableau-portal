<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';
    
    // Tidak ada timestamps di tabel
    public $timestamps = false;
    
    // Tidak ada remember_token di tabel, gunakan kolom login_session_key
    protected $rememberTokenName = 'login_session_key';

    protected $fillable = [
        'username',
        'password',
        'nama',
        'telp',
        'email',
        'foto',
        'account_status',
        'user_role_id',
    ];

    protected $hidden = [
        'password',
        'login_session_key',
    ];

    /**
     * Get the name attribute (alias untuk 'nama')
     */
    public function getNameAttribute()
    {
        return $this->nama;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->user_role_id == 1;
    }
}

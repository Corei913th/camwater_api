<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;



class Utilisateur extends Authenticatable
{
  use HasFactory, Notifiable, HasApiTokens;

  protected $table = 'utilisateurs';


  protected $fillable = [
    'email',
    'password',
    'role',
  ];

  protected $casts = [
    'role' => Role::class,
    'email_verifie_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  // Override Laravel's default password field
  public function getAuthPassword()
  {
    return $this->password;
  }


  public function getEmailForVerification()
  {
    return $this->email;
  }
}

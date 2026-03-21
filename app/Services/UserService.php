<?php

namespace App\Services;

use App\Models\Utilisateur;

class UserService
{
    public function __construct(
        private readonly Utilisateur $model
    ) {}

    /**
     * Trouver un user par l' email
     */
    public function findByEmail(string $email): Utilisateur
    {
        return $this->model->where('email', $email)->first();
    }
}

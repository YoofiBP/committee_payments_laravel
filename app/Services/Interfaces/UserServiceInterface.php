<?php


namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface UserServiceInterface extends ModelServiceInterface
{
    public function findByEmail(string $email);

    public function validateCredentials($credentials, $remember = false):bool;

    public function updateContribution(Model $user, array $contribution);
}

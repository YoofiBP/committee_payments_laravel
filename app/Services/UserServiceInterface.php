<?php


namespace App\Services;

use Illuminate\Database\Eloquent\Model;

interface UserServiceInterface
{
    public function add(array $attributes);

    public function findByEmail(string $email);

    public function validateCredentials($credentials, $remember = false):bool;

    public function update(Model $user, array $data);

    public function delete(Model $user);

    public function all();

    public function updateContribution(Model $user, array $contribution);
}

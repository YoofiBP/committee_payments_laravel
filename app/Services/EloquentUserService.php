<?php


namespace App\Services;


use App\Exceptions\DuplicateUserException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EloquentUserService implements UserServiceInterface
{

    public function add(array $attributes)
    {
        $user = $this->findByEmail($attributes['email']);
        if ($user !== null) {
            throw new DuplicateUserException();
        } else {
            return User::create($attributes);
        }

    }

    public function findByEmail(string $email)
    {
        return User::where('email', '=', $email)->first();
    }

    public function validateCredentials($credentials, $remember = false): bool
    {
        return Auth::guard('web')->attempt($credentials, $remember);
    }

    public function update(Model $user, array $data)
    {
        $user->fill($data)->save();
        return $user->refresh();
    }

    public function delete(Model $user)
    {
        try {
            return $user->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function all()
    {
        return User::all();
    }
}

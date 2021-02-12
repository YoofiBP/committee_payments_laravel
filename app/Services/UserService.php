<?php


namespace App\Services;


use App\Exceptions\DuplicateUserException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;

class UserService implements UserServiceInterface
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
}

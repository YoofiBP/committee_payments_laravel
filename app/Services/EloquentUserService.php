<?php


namespace App\Services;


use App\Exceptions\DuplicateUserException;
use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Traits\ImplementsCrudActions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EloquentUserService implements UserServiceInterface
{
    use ImplementsCrudActions;

    protected User $model;

    public function __construct(User $user){
        $this->model = $user;
    }

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

    public function updateContribution(Model $user, array $contribution)
    {
       //
    }
}

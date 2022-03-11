<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


// --------------------------------------------------------------------------------------------------------------------------------------------------|
// ------------------------------------------------------------------------------------------------------------------------------- User Repository --|
// --------------------------------------------------------------------------------------------------------------------------------------------------|
class UserRepository
{
    /* ------------------------------------------------------------------------------------------------------------------------------ user List -+- */
    /**
     * List all users.
     *
     * @return Collection|array
     */
    public function userList(): Collection|array
    {
        return User::whereNotAdmin()->get();
    }
    
    /* ---------------------------------------------------------------------------------------------------------------------------- user Delete -+- */
    public function userDelete(string $uuid)
    {
        $result = User::whereNotAdmin()->where('uuid', $uuid)->delete();
        
        if (!$result) {
            throw new ConflictHttpException('User does not exist!', null, 409);
        }
    }
    
}

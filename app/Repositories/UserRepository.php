<?php

namespace App\Repositories;

use App\DataTransferObjects\UserCreateDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;


// --------------------------------------------------------------------------------------------------------------------------------------------------|
// ------------------------------------------------------------------------------------------------------------------------------- User Repository --|
// --------------------------------------------------------------------------------------------------------------------------------------------------|
class UserRepository
{
    
    /* ---------------------------------------------------------------------------------------------------------------------------- get Details -+- */
    /**
     * Get user details.
     *
     * @param string $uuid
     *
     * @return User
     */
    public function getDetails(string $uuid) : User
    {
        /** @var User $user  */
        $user = User::whereUuid($uuid)->firstOrFail();
        
        return $user;
    }
    
    /* --------------------------------------------------------------------------------------------------------------------------------- create -+- */
    public function create(UserCreateDto $dto): User
    {
        return $this->save(new User(), $dto);
    }
    
    
    /* --------------------------------------------------------------------------------------------------------------------------------- update -+- */
    public function update(User|string $user, UserCreateDto $dto): User
    {
        if (is_string($user)) {
            $user = User::whereNotAdmin()->whereUuid($user)->firstOrFail();
        }
        
        return $this->save($user, $dto);
    }
    
    
    /* ----------------------------------------------------------------------------------------------------------------------------------- save -+- */
    public function save(User $user, UserCreateDto $dto): User
    {
        $user->forceFill($dto->toArray());
        
        if (isset($dto->password)) {
            $user->password = bcrypt($dto->password);
        }
        
        $user->save();
        
        return $user;
    }
    
    
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
    public function delete(User|string $user)
    {
        if (is_string($user)) {
            $user = User::whereNotAdmin()->whereUuid($user)->firstOrFail();
        }
        
        $user->delete();
    }
    
}

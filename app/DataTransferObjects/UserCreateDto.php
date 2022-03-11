<?php

namespace App\DataTransferObjects;

final class UserCreateDto extends BaseDto
{
    protected bool $autoFillFromRequest = true;
    
    public string $email;
    public string $first_name;
    public string $last_name;
    public string $password;
    public string $address;
    public string $avatar;
    public string $phone_number;
    public bool $is_marketing;
    public bool $is_admin;
}

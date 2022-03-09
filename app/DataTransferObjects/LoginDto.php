<?php

namespace App\DataTransferObjects;

class LoginDto extends BaseDto
{
    protected bool $autoFillFromRequest = true;
    
    public string $email;
    public string $password;
}

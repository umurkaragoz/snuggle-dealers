<?php

return [
    // Private key to be used on token creation.
    'private_key' => file_get_contents(storage_path('keys/jwt')),
    
    // Public key to be used on token verification, e.g. authentication & authorization.
    'public_key'  => file_get_contents(storage_path('keys/jwt.pub')),
    
    // Lifetime of tokens in seconds. After this period, tokens will be invalid.
    'ttl' => 7200,
];

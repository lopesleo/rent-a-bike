<?php
// generate_hash.php

if ($argc !== 2) {
    fwrite(STDERR, "Uso: php {$argv[0]} <senha>\n");
    exit(1);
}

$password = $argv[1];

$salt = bin2hex(random_bytes(16));

$pepper = 'k#pW7$@ZgR9b!qN4sT8u&v*Yx%C2e(H5';

$hash = hash('sha512', $salt . $password . $pepper);

echo "SALT: $salt\n";
echo "HASH: $hash\n";

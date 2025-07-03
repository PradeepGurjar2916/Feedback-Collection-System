<?php
// generate_hash.php
$password = 'admin123'; // Change this to your desired password
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Password: " . $password . "\n";
echo "Hash: " . $hash;
?>
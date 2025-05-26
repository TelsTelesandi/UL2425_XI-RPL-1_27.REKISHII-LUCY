<?php
$plaintext = "guru123";
$hash = password_hash($plaintext, PASSWORD_DEFAULT);
echo $hash;
?>

<?php
global $php;
$user = new App\Auth($php->config['user']);
return $user;

<?php

require('./classes/Root/Core.php');

Root\Core::initialize();

set_exception_handler([ App\Exceptions\HttpException::class, 'handler' ]);

// Affichage de la réponse de la requête
echo Root\Request::current()->response();

?>
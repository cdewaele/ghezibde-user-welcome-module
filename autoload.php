<?php

declare(strict_types=1);

namespace GhezibdeUserWelcome;

use Composer\Autoload\ClassLoader;

//Autoload this webtrees custom module
$loader = new ClassLoader(__DIR__);
// Register the namespace for the module
$loader->addPsr4('GhezibdeUserWelcome\\', __DIR__);
$loader->register();

return true;

<?php

declare(strict_types=1);

namespace GhezibdeUserWelcome;

use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Container\Container;

if (!require_once __DIR__ . '/autoload.php') {
    return null;
}

$container = Container::getInstance();

return new GhezibdeUserWelcomeModule(
    $container->make(ModuleService::class)
);

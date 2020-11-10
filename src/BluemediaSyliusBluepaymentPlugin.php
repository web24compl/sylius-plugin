<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BluemediaSyliusBluepaymentPlugin extends Bundle
{
    public const VERSION = '1.0.0';

    use SyliusPluginTrait;
}

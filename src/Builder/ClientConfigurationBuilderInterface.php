<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Builder;

use Payum\Core\Bridge\Spl\ArrayObject;
use Bluemedia\SyliusBluepaymentPlugin\ValueObject\ClientConfiguration;

interface ClientConfigurationBuilderInterface
{
    public static function build(ArrayObject $payumConfig): ClientConfiguration;
}

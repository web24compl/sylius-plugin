<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Factory;

use Bluemedia\SyliusBluepaymentPlugin\Factory\BluepaymentGatewayFactory;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Configuration;
use Payum\Core\GatewayFactory;
use PhpSpec\ObjectBehavior;

class BluepaymentGatewayFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BluepaymentGatewayFactory::class);
        $this->shouldHaveType(GatewayFactory::class);
    }

    function it_populateConfig_run(): void
    {
        $this->createConfig(Configuration::getClientConfiguration());
    }
}

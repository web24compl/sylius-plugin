<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Builder;

use Bluemedia\SyliusBluepaymentPlugin\ValueObject\ClientConfiguration;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Configuration;
use Payum\Core\Bridge\Spl\ArrayObject;
use PhpSpec\ObjectBehavior;

class ClientConfigurationBuilderSpec extends ObjectBehavior
{
    public function it_build_client_configuration(ArrayObject $payumConfig): void
    {
        $configFixture = Configuration::getClientConfiguration();
        $payumConfig->toUnsafeArray()->willReturn($configFixture);
        $payumConfig->get('test_mode')->willReturn(true);

        $clientConfig = $this::build($payumConfig);

        $clientConfig->shouldBeAnInstanceOf(ClientConfiguration::class);
        $clientConfig->getGatewayUrl()->shouldBe(Configuration::GATEWAY_URL);
        $clientConfig->getServiceId('PLN')->shouldBe($configFixture['service_id_PLN']);
        $clientConfig->getSharedKey('PLN')->shouldBe($configFixture['shared_key_PLN']);
    }
}

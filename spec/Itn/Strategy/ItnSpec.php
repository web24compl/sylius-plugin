<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy;

use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Itn;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\ItnStrategyInterface;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\ItnResponseType;
use PhpSpec\ObjectBehavior;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Itn\ItnTestEnum;

class ItnSpec extends ObjectBehavior
{
    private const RESULT = [
        'order_state' => 'pay',
        'payment_state' => 'complete',
    ];

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Itn::class);
    }

    public function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ItnStrategyInterface::class);
    }

    public function it_returns_statuses(ItnResponseType $responseType): void
    {
        $responseType->canProcess(ItnTestEnum::TRANSACTION_STATUS_SUCCESS, ItnTestEnum::PAYMENT_STATE_NEW)
            ->willReturn(true);

        $responseType->process()->willReturn(self::RESULT);
        $this->add($responseType);

        $result = $this->handle(ItnTestEnum::TRANSACTION_STATUS_SUCCESS, ItnTestEnum::PAYMENT_STATE_NEW);

        $result->shouldBe(self::RESULT);
    }

    public function it_returns_empty_array_when_cant_process(ItnResponseType $responseType): void
    {
        $responseType->canProcess(ItnTestEnum::TRANSACTION_STATUS_SUCCESS, ItnTestEnum::PAYMENT_STATE_NEW)
            ->willReturn(false);

        $result = $this->handle(ItnTestEnum::TRANSACTION_STATUS_FAILURE, ItnTestEnum::PAYMENT_STATE_NEW);

        $result->shouldBe([]);
    }
}

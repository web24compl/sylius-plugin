<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result;

use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\ItnResponseType;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\Pending;
use PhpSpec\ObjectBehavior;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Itn\ItnTestEnum;

class PendingSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Pending::class);
    }

    public function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ItnResponseType::class);
    }

    public function it_returns_true_on_can_process(): void
    {
        $result = $this->canProcess(ItnTestEnum::TRANSACTION_STATUS_PENDING, ItnTestEnum::PAYMENT_STATE_NEW);

        $result->shouldBe(true);
    }

    public function it_returns_false_on_can_process(): void
    {
        $result = $this->canProcess(ItnTestEnum::TRANSACTION_STATUS_PENDING, ItnTestEnum::PAYMENT_STATE_PROCESSING);

        $result->shouldBe(false);
    }

    public function it_process_returns_expected_states(): void
    {
        $result = $this->process();

        $result->shouldBe([
            'order_state' => 'request_payment',
            'payment_state' => 'process',
        ]);
    }
}

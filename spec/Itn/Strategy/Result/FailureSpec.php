<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result;

use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\Failure;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\ItnResponseType;
use PhpSpec\ObjectBehavior;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Itn\ItnTestEnum;

class FailureSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Failure::class);
    }

    public function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ItnResponseType::class);
    }

    public function it_returns_true_on_can_process(): void
    {
        $result = $this->canProcess(ItnTestEnum::TRANSACTION_STATUS_FAILURE, ItnTestEnum::PAYMENT_STATE_FAIL);
        $result->shouldBe(true);

        $result = $this->canProcess(ItnTestEnum::TRANSACTION_STATUS_FAILURE, ItnTestEnum::PAYMENT_STATE_PROCESSING);
        $result->shouldBe(true);
    }

    public function it_returns_false_on_can_process(): void
    {
        $result = $this->canProcess(ItnTestEnum::TRANSACTION_STATUS_FAILURE, ItnTestEnum::PAYMENT_STATE_NEW);

        $result->shouldBe(false);
    }

    public function it_process_returns_expected_states(): void
    {
        $result = $this->process();

        $result->shouldBe([
            'order_state' => 'request_payment',
            'payment_state' => 'fail',
        ]);
    }
}

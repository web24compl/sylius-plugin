<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result;

use PhpSpec\ObjectBehavior;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\Success;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Itn\ItnTestEnum;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\ItnResponseType;

class SuccessSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Success::class);
    }

    public function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ItnResponseType::class);
    }

    public function it_returns_true_on_can_process(): void
    {
        foreach (ItnTestEnum::PAYMENT_STATUSES as $paymentStatus) {
            $result = $this->canProcess(ItnTestEnum::TRANSACTION_STATUS_SUCCESS, $paymentStatus);

            $result->shouldBe(true);
        }
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
            'order_state' => 'pay',
            'payment_state' => 'complete',
        ]);
    }
}

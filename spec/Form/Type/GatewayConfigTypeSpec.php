<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Form\Type;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Bluemedia\SyliusBluepaymentPlugin\Form\Type\GatewayConfigType;

class GatewayConfigTypeSpec extends ObjectBehavior
{
    public function let(RepositoryInterface $currencyRepository): void
    {
        $this->beConstructedWith($currencyRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(GatewayConfigType::class);
        $this->shouldHaveType(AbstractType::class);
    }

    public function it_build_form(
        $currencyRepository,
        CurrencyInterface $currency,
        FormBuilderInterface $formBuilder
    ): void {
        $currency->getCode()->shouldBeCalledOnce()->willReturn('PLN');
        $currencyRepository->findAll()->shouldBeCalledOnce()->willReturn([$currency]);

        $formBuilder->add('test_mode', ChoiceType::class, Argument::type('array'))
            ->shouldBeCalledOnce()
            ->willReturn($formBuilder);

        $formBuilder->add('currency_0', HiddenType::class, ['data' => 'PLN'])
            ->shouldBeCalledOnce()
            ->willReturn($formBuilder);

        $formBuilder->add(
            Argument::any(),
            Argument::any(),
            Argument::any()
        )
            ->shouldBeCalled()
            ->willReturn($formBuilder);

        $this->buildForm($formBuilder, []);
    }
}

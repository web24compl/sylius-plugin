<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Transaction\Controller;

use PhpSpec\ObjectBehavior;
use Payum\Core\Reply\HttpRedirect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Bluemedia\SyliusBluepaymentPlugin\Transaction\Controller\TransactionConfirmationController;

class TransactionConfirmationControllerSpec extends ObjectBehavior
{
    public function let(
        OrderRepositoryInterface $orderRepository,
        RouterInterface $router
    ): void {
        $this->beConstructedWith($orderRepository, $router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TransactionConfirmationController::class);
        $this->shouldHaveType(AbstractController::class);
    }

    public function it_throws_home_page_redirect(
        Request $request,
        ParameterBag $parameterBag,
        $orderRepository,
        $router
    ): void {
        $parameterBag->get('OrderID')->willReturn(123);
        $orderRepository->find(123)->willReturn(null);
        $router->generate('sylius_shop_homepage')->willReturn('http://homepage');
        $request->query = $parameterBag;

        $this->shouldThrow(HttpRedirect::class)
            ->during('processConfirmation', [$request]);
    }

    public function it_throws_thank_you_redirect(
        Request $request,
        ParameterBag $parameterBag,
        SessionInterface $session,
        OrderInterface $order,
        $orderRepository,
        $router
    ): void {
        $parameterBag->get('OrderID')->willReturn(123);
        $orderRepository->find(123)->willReturn($order);

        $session->set('sylius_order_id', 123)->shouldBeCalledOnce();
        $request->getSession()->willReturn($session);

        $order->getLocaleCode()->willReturn('pl_PL');

        $router->generate('sylius_shop_order_thank_you', ['_locale' => 'pl_PL'])
            ->willReturn('http://thankyou');

        $request->query = $parameterBag;

        $this->shouldThrow(HttpRedirect::class)
            ->during('processConfirmation', [$request]);
    }
}

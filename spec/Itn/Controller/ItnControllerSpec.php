<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Itn\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Payum\Core\Storage\StorageInterface;
use Payum\Core\Model\GatewayConfigInterface;
use SM\Factory\Factory;
use SM\StateMachine\StateMachineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Itn\Itn;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Controller\ItnController;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Configuration;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\ItnStrategyInterface;

/**
 * @mixin ItnController
 */
class ItnControllerSpec extends ObjectBehavior
{
    public function let(
        OrderRepositoryInterface $orderRepository,
        StorageInterface $storage,
        GatewayConfigInterface $gatewayConfig,
        PaymentRepositoryInterface $paymentRepository,
        LoggerInterface $logger
    ): void {
        $gatewayConfig->getConfig()->willReturn(Configuration::getClientConfiguration());

        $storage->findBy(['factoryName' => 'bluepayment'])->willReturn([$gatewayConfig]);

        $this->beConstructedWith($orderRepository, $storage, $paymentRepository, $logger);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ItnController::class);
        $this->shouldHaveType(AbstractController::class);
    }

    public function it_returns_xml_response_on_process_action(
        OrderInterface $order,
        PaymentInterface $payment,
        ItnStrategyInterface $itnStrategy,
        Factory $stateMachineFactory,
        StateMachineInterface $stateMachine,
        StateMachineInterface $orderStateMachine,
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        $orderRepository
    ): void {
        $_POST['transactions'] = Itn::getItnInRequest();

        $payment->getState()->willReturn('processing');
        $order->getTotal()->willReturn(50000);
        $order->getLastPayment()->willReturn($payment);
        $orderRepository->find(123)->willReturn($order);

        $itnStrategy->handle('SUCCESS', 'processing')->willReturn([
            'order_state' => 'pay',
            'payment_state' => 'complete',
        ]);

        $stateMachine->can('complete')->willReturn(true);
        $stateMachine->apply('complete')->shouldBeCalled();

        $stateMachineFactory->get($payment, 'sylius_payment')->willReturn($stateMachine);
        $orderStateMachine->can('pay')->willReturn(true);
        $orderStateMachine->apply('pay')->shouldBeCalled();

        $stateMachineFactory->get($order, 'sylius_order_payment')->willReturn($orderStateMachine);

        $container->get('sm.factory')->willReturn($stateMachineFactory);

        $entityManager->flush()->shouldBeCalled();
        $container->get('sylius.manager.payment')->willReturn($entityManager);

        $entityManager->flush()->shouldBeCalled();
        $container->get('sylius.manager.order')->willReturn($entityManager);

        $this->setContainer($container);

        $response = $this->processAction();
        $response->shouldHaveType(Response::class);
        $response->getContent()->shouldContain('<confirmation><![CDATA[CONFIRMED]]></confirmation>');
    }

    public function it_returns_xml_not_confirmed_response_on_wrong_hash(): void
    {
        $_POST['transactions'] = Itn::getItnInRequestWrongHash();;

        $response = $this->processAction();

        $response->shouldHaveType(Response::class);
        $response->getContent()->shouldContain('<confirmation><![CDATA[NOTCONFIRMED]]></confirmation>');
    }

    public function it_returns_xml_not_confirmed_on_exception(
        OrderInterface $order,
        $orderRepository
    ): void {
        $_POST['transactions'] = Itn::getItnInRequest();

        $order->getTotal()->willReturn(50001);
        $order->getLastPayment()->willReturn(null);
        $orderRepository->find(123)->willReturn($order);

        $response = $this->processAction();

        $response->shouldHaveType(Response::class);
        $response->getContent()->shouldContain('<confirmation><![CDATA[NOTCONFIRMED]]></confirmation>');
    }
}

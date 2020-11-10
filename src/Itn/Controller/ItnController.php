<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Itn\Controller;

use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Itn;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\Failure;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\Pending;
use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\Success;
use Bluemedia\SyliusBluepaymentPlugin\Util\Formatter\PriceFormatter;
use Bluemedia\SyliusBluepaymentPlugin\Util\ParamSuffixer;
use Psr\Log\LoggerInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Payum\Core\Storage\StorageInterface;
use BlueMedia\Common\Util\XMLParser;
use BlueMedia\Client;

final class ItnController extends AbstractController
{
    /** @var StorageInterface */
    private $gatewayConfigStore;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StorageInterface $gatewayConfigStore,
        PaymentRepositoryInterface $paymentRepository,
        LoggerInterface $logger
    ) {
        $this->gatewayConfigStore = $gatewayConfigStore;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->logger = $logger;
    }

    public function processAction()
    {
        $transactions = $_POST['transactions'];
        $transaction = XMLParser::parse(base64_decode($_POST['transactions'], true));
        $xmlData = (array)$transaction->transactions->transaction;

        $connectionData = $this->getDataByCurrency($xmlData['currency']);

        $client = new Client($connectionData['service_id'], $connectionData['shared_key']);

        try {
            $itnIn = $client->doItnIn($transactions);
            $itnIn->getData()->setServiceId($connectionData['service_id']);
            $transactionConfirmed = $client->checkHash($itnIn->getData());

            $this->logger->error('[BM Bluepayment] ITN Input XML', [
                'payload' => json_encode($itnIn->getData()->toArray()),
                'transactionConfirmed' => $transactionConfirmed,
            ]);

            if ($transactionConfirmed) {
                $orderId = (int)ParamSuffixer::removeSuffix($itnIn->getData()->getOrderId());
                $order = $this->orderRepository->find($orderId);

                if (!(PriceFormatter::toDecimals($order->getTotal()) === $itnIn->getData()->getAmount())) {
                    throw new \Exception(
                        sprintf(
                            'Data is inconsistent. Transaction amount %s differs from orderd amount %s.',
                            $itnIn->getData()->getAmount(),
                            PriceFormatter::toDecimals($order->getTotal())
                        )
                    );
                }

                $payment = $order->getLastPayment();

                $itn = new Itn();
                $itn->add(new Success());
                $itn->add(new Failure());
                $itn->add(new Pending());
                $states = $itn->handle($itnIn->getData()->getPaymentStatus(), $payment->getState());

                $this->logger->error('[BM Bluepayment] ITN payment/order states', [
                    'states' => json_encode($states),
                ]);

                if (empty($states) === false) {
                    $this->applyChanges($states, $payment, $order);
                }
            }
        } catch (\Throwable $exception) {
            $this->logger->error('[BM Bluepayment] ITN Error', [
                'exception' => $exception,
            ]);

            $transactionConfirmed = false;
        }

        $itnResponse = $client->doItnInResponse($itnIn->getData(), $transactionConfirmed);

        $this->logger->error('[BM Bluepayment] ITN Response XML ', [
            'payload' => $itnResponse->getData()->toXml(),
        ]);

        $response = new Response($itnResponse->getData()->toXml());
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    private function applyTransition(StateMachineInterface $paymentStateMachine, string $transition): void
    {
        if ($paymentStateMachine->can($transition)) {
            $paymentStateMachine->apply($transition);
        }
    }

    private function getDataByCurrency(string $currency): array
    {
        $gatewayConfiguration = $this->gatewayConfigStore->findBy(['factoryName' => 'bluepayment'])[0];

        return [
            'service_id' => (string) $gatewayConfiguration->getConfig()['service_id_' .  $currency],
            'shared_key' => (string) $gatewayConfiguration->getConfig()['shared_key_' . $currency],
        ];
    }

    private function applyChanges(array $result, $payment, $order): void
    {
        $stateMachineFactory = $this->container->get('sm.factory');

        $paymentStateMachine = $stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        $this->applyTransition($paymentStateMachine, $result['payment_state']);
        $this->container->get('sylius.manager.payment')->flush();

        $orderStateMachine = $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        $this->applyTransition($orderStateMachine, $result['order_state']);
        $this->container->get('sylius.manager.order')->flush();
    }
}

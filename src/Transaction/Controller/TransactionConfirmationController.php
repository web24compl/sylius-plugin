<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Transaction\Controller;

use Payum\Core\Reply\HttpRedirect;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Bluemedia\SyliusBluepaymentPlugin\Util\ParamSuffixer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TransactionConfirmationController extends AbstractController
{
    private $orderRepository;
    private $router;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RouterInterface $router
    ) {
        $this->orderRepository = $orderRepository;
        $this->router = $router;
    }

    public function processConfirmation(Request $request)
    {
        $orderId = ParamSuffixer::removeSuffix((string) $request->query->get('OrderID'));

        $order = $this->orderRepository->find($orderId);

        if (is_null($order)) {
            throw new HttpRedirect($this->router->generate('sylius_shop_homepage'));
        }

        $request->getSession()->set('sylius_order_id', $orderId);

        throw new HttpRedirect(
            $this->router->generate('sylius_shop_order_thank_you', ['_locale' => $order->getLocaleCode()])
        );
    }
}

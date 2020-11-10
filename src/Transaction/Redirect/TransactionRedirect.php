<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Transaction\Redirect;

use Payum\Core\Reply\HttpResponse;

final class TransactionRedirect extends HttpResponse
{
    public function __construct(string $content)
    {
        parent::__construct($content, 302);
    }
}

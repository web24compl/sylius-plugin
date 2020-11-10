<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Itn\Enum;

abstract class ItnEnum
{
    public const PAYMENT_STATUS_PENDING = 'PENDING';
    public const PAYMENT_STATUS_SUCCESS = 'SUCCESS';
    public const PAYMENT_STATUS_FAILURE = 'FAILURE';

    public const PAYMENT_STATE_PROCESSING = 'processing';
    public const PAYMENT_STATE_FAIL = 'fail';
    public const PAYMENT_STATE_NEW = 'new';
}

<?php
declare(strict_types=1);

namespace Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Itn;

abstract class ItnTestEnum
{
    public const TRANSACTION_STATUS_PENDING = 'PENDING';
    public const TRANSACTION_STATUS_SUCCESS = 'SUCCESS';
    public const TRANSACTION_STATUS_FAILURE = 'FAILURE';

    public const PAYMENT_STATE_PROCESSING = 'processing';
    public const PAYMENT_STATE_FAIL = 'fail';
    public const PAYMENT_STATE_NEW = 'new';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_STATE_NEW,
        self::PAYMENT_STATE_PROCESSING,
        self::PAYMENT_STATE_FAIL
    ];
}

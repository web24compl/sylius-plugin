<?php
declare(strict_types=1);

namespace Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Itn;

abstract class Itn
{
    public static function getItnInRequest(): string
    {
        return base64_encode(file_get_contents(__DIR__ . '/ItnInRequest.xml'));
    }

    public static function getItnInRequestWrongHash(): string
    {
        return base64_encode(file_get_contents(__DIR__ . '/ItnInRequestWrongHash.xml'));
    }
}

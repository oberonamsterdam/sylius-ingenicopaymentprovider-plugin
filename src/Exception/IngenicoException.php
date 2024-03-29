<?php

declare(strict_types=1);

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Oberon\SyliusIngenicoPaymentProviderPlugin\Exception;

use Payum\Core\Exception\Http\HttpException;

final class IngenicoException extends HttpException
{
    public const LABEL = 'IngenicoException';

    public static function newInstance($status)
    {
        //todo check exception returned from ingenico
        $parts = [self::LABEL];

        if (property_exists($status, 'statusLiteral')) {
            $parts[] = '[reason literal] ' . $status->statusLiteral;
        }

        if (property_exists($status, 'statusCode')) {
            $parts[] = '[status code] ' . $status->statusCode;
        }

        if (property_exists($status, 'statusDesc')) {
            $parts[] = '[reason phrase] ' . $status->statusDesc;
        }

        $message = implode(\PHP_EOL, $parts);

        return new static($message);
    }
}

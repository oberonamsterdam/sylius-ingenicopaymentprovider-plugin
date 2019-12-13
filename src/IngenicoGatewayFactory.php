<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace Oberon\SyliusIngenicoPaymentProviderPlugin;

use Oberon\SyliusIngenicoPaymentProviderPlugin\Bridge\IngenicoBridgeInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class IngenicoGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults(
            [
                'payum.factory_name' => 'ingenico',
                'payum.factory_title' => 'Ingenico',
            ]
        );

        if (false === (bool) $config['payum.api']) {
            $config['payum.default_options'] = [
                'environment' => IngenicoBridgeInterface::SANDBOX_ENVIRONMENT,
                'pspid' => '',
                'sha_secret' => '',
                'accept_url' => '',
                'decline_url' => '',
                'exception_url' => '',
                'cancel_url' => '',
            ];

            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = ['environment', 'pos_id', 'signature_key', 'oauth_client_id', 'oauth_client_secret'];
            $config['payum.api'] = static function (ArrayObject $config): array {
                $config->validateNotEmpty($config['payum.required_options']);

                return [
                    'environment' => $config['environment'],
                    'pspid' => $config['pspid'],
                    'sha_secret' => $config['sha_secret'],
                    'accept_url' => $config['accept_url'],
                    'decline_url' => $config['decline_url'],
                    'exception_url' => $config['exception_url'],
                    'cancel_url' => $config['cancel_url'],
                ];
            };
        }
    }
}

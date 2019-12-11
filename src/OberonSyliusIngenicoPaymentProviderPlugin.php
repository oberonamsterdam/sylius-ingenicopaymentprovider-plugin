<?php

declare(strict_types=1);

namespace Oberon\SyliusIngenicoPaymentProviderPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OberonSyliusIngenicoPaymentProviderPlugin extends Bundle
{
    use SyliusPluginTrait;
}

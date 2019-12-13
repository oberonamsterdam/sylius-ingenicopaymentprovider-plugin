<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace Oberon\SyliusIngenicoPaymentProviderPlugin\Bridge;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\PaymentInterface;

final class IngenicoBridge implements ActionInterface, GatewayAwareInterface, IngenicoBridgeInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        /** @var Order $order */
        $order = $payment->getOrder();

        if ($payment->getDetails()) {
            $request->setResult($payment->getDetails());

            return;
        }

        $details = [];
        $details['ingenicoOrderId'] = $order->getNumber();
        $details['ingenicoAmount'] = $order->getTotal();
        $details['ingenicoCurrency'] = $order->getCurrencyCode();
        $details['ingenicoLanguage'] = $order->getLocaleCode();
        $details['ingenicoOwnerZip'] = $order->getCustomer()->getEmail();

        $customer = $order->getCustomer();

        if (null !== $customer) {
            $details['pgwEmail'] = $customer->getEmail();
            $details['ingenicoCN'] = $customer->getFirstName() . ' ' . $customer->getLastName();
            $details['ingenicoEmail'] = $customer->getEmail();
        }

        $billingAddress = $order->getBillingAddress();

        if (null !== $billingAddress) {
            $details['ingenicoCustomerName'] = $billingAddress->getFirstName() . ' ' . $billingAddress->getLastName();
            $details['ingenicoOwnerZip'] = $billingAddress->getPostcode();
            $details['ingenicoOwnerAddress'] = $billingAddress->getLastName();
            $details['ingenicoOwnerCountry'] = $billingAddress->getCountryCode();
            $details['ingenicoOwnerTown'] = $billingAddress->getCity();
            $details['ingenicoOwnerPhoneNumber'] = $billingAddress->getPhoneNumber();
        }


        $request->setResult($details);
    }


    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array';
    }
}

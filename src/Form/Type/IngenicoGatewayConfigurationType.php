<?php

declare(strict_types=1);

namespace Oberon\SyliusIngenicoPaymentProviderPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class IngenicoGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pspid', TextType::class, [
                'label' => 'oberon.sylius_ingenico_paymentprovider_plugin.pspid',
                'constraints' => [
                    new NotBlank([
                        'message' => 'oberon.sylius_ingenico_paymentprovider_plugin.pspid.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ]
            ])
            ->add('sha_secret', TextType::class, [
                'label' => 'oberon.sylius_ingenico_paymentprovider_plugin.sha_secret',
                'constraints' => [
                    new NotBlank([
                        'message' => 'oberon.sylius_ingenico_paymentprovider_plugin.sha_secret.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ]
            ])
            ->add('accept_url', UrlType::class, [
                'label' => 'oberon.sylius_ingenico_paymentprovider_plugin.accept_url',
                'constraints' => [
                    new NotBlank([
                        'message' => 'oberon.sylius_ingenico_paymentprovider_plugin.accept_url.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ]
            ])
            ->add('decline_url', UrlType::class, [
                'label' => 'oberon.sylius_ingenico_paymentprovider_plugin.decline_url',
                'constraints' => [
                    new NotBlank([
                        'message' => 'oberon.sylius_ingenico_paymentprovider_plugin.decline_url.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ]
            ])
            ->add('exception_url', UrlType::class, [
                'label' => 'oberon.sylius_ingenico_paymentprovider_plugin.exception_url',
                'constraints' => [
                    new NotBlank([
                        'message' => 'oberon.sylius_ingenico_paymentprovider_plugin.exception_url.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ]
            ])
            ->add('cancel_url', UrlType::class, [
                'label' => 'oberon.sylius_ingenico_paymentprovider_plugin.cancel_url',
                'constraints' => [
                    new NotBlank([
                        'message' => 'oberon.sylius_ingenico_paymentprovider_plugin.cancel_url.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ]
            ])
        ;
    }
}

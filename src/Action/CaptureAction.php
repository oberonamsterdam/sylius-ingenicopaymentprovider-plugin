<?php

declare(strict_types=1);

//todo make this work

namespace Oberon\SyliusIngenicoPaymentProviderPlugin\Action;

use Ingenico\Connect\Sdk\Client;
use Ingenico\Connect\Sdk\Communicator;
use Ingenico\Connect\Sdk\CommunicatorConfiguration;
use Ingenico\Connect\Sdk\DefaultConnection;
use Oberon\SyliusIngenicoPaymentProviderPlugin\Bridge\IngenicoBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\RenderTemplate;

class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    protected $api;

    /** @var IngenicoBridgeInterface */
    private $ingenicoBridge;

    public function __construct(IngenicoBridgeInterface $ingenicoBridge)
    {
        $this->ingenicoBridge = $ingenicoBridge;
        $this->initClient();
    }

    private function initClient()
    {
//        GatewayConfig::
//        $this->gateway->
        $communicatorConfiguration = new CommunicatorConfiguration(
            '','','',''
        );
        $connection = new DefaultConnection();
        $communicator = new Communicator($connection, $communicatorConfiguration);

        $client = new Client($communicator);
        $client->setClientMetaInfo("consumer specific JSON meta info");
        $this->api = $client;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request) :void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $httpRequest = new GetHttpRequest();
        $this->gateway->execute($httpRequest);

        //we are back from ht payway site so we have to just update model and complete action
        if (isset($httpRequest->request['pgw_trace_ref'])) {
            $model['tcompayway_response'] = $this->checkAndUpdateResponse($httpRequest->request);

            return;
        }

        $this->api->setPgwAmount($model['pgwAmount']);
        $this->api->setPgwOrderId($model['pgwOrderId']);
        $this->api->setPgwEmail($model['pgwEmail']);
        $this->api->setPgwSuccessUrl($request->getToken()->getTargetUrl());
        $this->api->setPgwFailureUrl($request->getToken()->getTargetUrl());

        $this->api->setPgwFirstName($model['pgwFirstName']);
        $this->api->setPgwLastName($model['pgwLastName']);
        $this->api->setPgwStreet($model['pgwStreet']);
        $this->api->setPgwCity($model['pgwCity']);
        $this->api->setPgwPostCode($model['pgwPostCode']);
        $this->api->setPgwCountry($model['pgwCountry']);
        $this->api->setPgwPhoneNumber($model['pgwPhoneNumber']);

        $this->api->setPgwLanguage($model['pgwLanguage']);
        $this->api->setPgwMerchantData($model['pgwMerchantData']);
        $this->api->setPgwOrderInfo($model['pgwOrderInfo']);
        $this->api->setPgwOrderItems($model['pgwOrderItems']);

        $renderTemplate = new RenderTemplate(
            '@LocasticSyliusHTPayWayPlugin/Offsite/capture.html.twig', array(
                'payment' => $this->api,
            )
        );
        $this->gateway->execute($renderTemplate);

        throw new HttpResponse($renderTemplate->getResult());
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Capture && $request->getModel() instanceof ArrayObject;
    }

    protected function checkAndUpdateResponse($pgwResponse)
    {
        if (!$this->api->isPgwResponseValid($pgwResponse)) {
            throw new RequestNotSupportedException('Not valid PGW Response');
        }

        // tcompayway request failed
        if (isset($pgwResponse['pgw_result_code'])) {
            $pgwResponse['error'] = ResponseCodeInterpreter::getPgwResultCode($pgwResponse['pgw_result_code']);

            return $pgwResponse;
        }

        // tcom request success, add status code 0 manually
        $pgwResponse['credit_card'] = CardTypeInterpreter::getPgwCardType($pgwResponse['pgw_card_type_id']);
        $pgwResponse['pgw_result_code'] = 0;

        return $pgwResponse;
    }
}

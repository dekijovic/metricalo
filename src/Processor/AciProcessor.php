<?php

namespace App\Processor;

use App\Dto\PaymentDto;
use App\Exception\ProcessorException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AutoconfigureTag('processor.aci')]
class AciProcessor implements ProcessorInterface
{

    const ENTITY_ID = '8a8294174b7ecb28014b9699220015ca';

    public function __construct(private readonly HttpClientInterface $aciClient)
    {}
    public function payment(PaymentDto $dto)
    {
        $id = $this->preAuthorize($dto);
        $this->capture($dto, $id);
    }

    public function preAuthorize(PaymentDto $dto):string
    {
        $request = $this->aciClient->request(
            'POST',
            '/v1/payments',
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    'entityId'         => self::ENTITY_ID,
                    "amount"           => $dto->amount,
                    "currency"         => $dto->currency,
                    'paymentBrand'     => $dto->aci->paymentBrand,
                    'paymentType'      => 'PA',
                    'card.number'      => $dto->aci->cardNumber,
                    'card.holder'      => $dto->aci->cardHolder,
                    'card.expiryMonth' => $dto->aci->cardExpiryMonth,
                    'card.expiryYear'  => $dto->aci->cardExpiryYear,
                    'card.cvv'         => $dto->aci->cardCvv,
                ]
            ]
        );

        if($request->getStatusCode() == 200){
            return $request->toArray()['id'];
        }else{
            $badResponse = json_decode($request->getContent(false), true);
            $msg = '';
            foreach ($badResponse['result']['parameterErrors'] as $err){
                $msg .=$err['message'];
            }

            throw new ProcessorException($msg, 400);
        }
    }

        public function capture(PaymentDto $dto, $id):void
    {
        $response = $this->aciClient->request(
            'POST',
            '/v1/payments/'.$id,
            [
                'body' => [
                    "amount"=> $dto->amount,
                    "currency" => $dto->currency,
                    'paymentType' => "CP",
                    'entityId' => self::ENTITY_ID,
                ]
            ]
        );

        if($response->getStatusCode() !== 200){
            $badResponse = json_decode($response->getContent(false), true);
            $msg = '';
            foreach ($badResponse['result']['parameterErrors'] as $err){
                $msg .=$err['message'];
            }

            throw new ProcessorException($msg, 400);
        }


    }

}
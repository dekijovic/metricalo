<?php

namespace App\Processor;

use App\Dto\PaymentDto;
use App\Exception\ProcessorException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AutoconfigureTag('processor.shift4')]
class Shift4Processor implements ProcessorInterface
{

    public function __construct(private readonly HttpClientInterface $shift4Client)
    {}

    public function payment(PaymentDto $dto):void
    {
        $this->createNewCharge($dto);

    }

    public function createNewCharge(PaymentDto $dto):void
    {
        $response = $this->shift4Client->request(
            'POST',
            '/charges',
            [
                'body' => [
                    "amount"=> $dto->amount,
                    "currency" =>$dto->currency,
                    'customerId' => $dto->shift4->customer,
                    'card' => $dto->shift4->card,
                    "description"=>"Example charge"
                ]
            ]
        );

        if($response->getStatusCode() !== 200){

            $badResponse = json_decode($response->getContent(false), true);
            throw new ProcessorException($badResponse['error']['message'] , 400);
        }

    }

}
<?php

namespace App\Tests\Processor;

use App\Dto\PaymentAciDto;
use App\Dto\PaymentDto;
use App\Exception\ProcessorException;
use App\Processor\AciProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AciProcessorTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private AciProcessor $aciProcessor;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->aciProcessor = new AciProcessor($this->httpClient);
    }

    public function testPayment(): void
    {
        $dto = $this->createPaymentDto();

        $preAuthResponse = $this->createMock(ResponseInterface::class);
        $preAuthResponse->method('getStatusCode')->willReturn(200);
        $preAuthResponse->method('toArray')->willReturn(['id' => 'test-id']);

        $captureResponse = $this->createMock(ResponseInterface::class);
        $captureResponse->method('getStatusCode')->willReturn(200);

        $invokedCount = $this->exactly(2);
        $this->httpClient
            ->expects($invokedCount)
            ->method('request')
            ->willReturnCallback(function ($parameters) use ($invokedCount, $preAuthResponse, $captureResponse) {
                if ($invokedCount->getInvocationCount() === 1) {
                    return $preAuthResponse;
                }

                if ($invokedCount->getInvocationCount() === 2) {
                    return $preAuthResponse;
                }
});

        $this->aciProcessor->payment($dto);

        // No exceptions
        $this->assertTrue(true);
    }

    public function testPreAuthorizeThrowsProcessorException(): void
    {
        $dto = $this->createPaymentDto();

        $errorResponse = $this->createMock(ResponseInterface::class);
        $errorResponse->method('getStatusCode')->willReturn(400);
        $errorResponse->method('getContent')->willReturn(json_encode([
            'result' => [
                'parameterErrors' => [
                    ['message' => 'Invalid card number.']
                ]
            ]
        ]));

        $this->httpClient
            ->method('request')
            ->willReturn($errorResponse);

        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('Invalid card number.');

        $this->aciProcessor->payment($dto);
    }

    private function createPaymentDto(): PaymentDto
    {
        $dto = new PaymentDto(
            amount: '100.00', currency: 'USD', shift4: null, aci: new PaymentAciDto (
                paymentBrand: 'VISA',
                cardNumber: '4111111111111111',
                cardHolder: 'John Doe',
                cardExpiryMonth: '12',
                cardExpiryYear: '2025',
                cardCvv: '123',
            )
        );

        return $dto;
    }
}

<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;
class PaymentAciDto
{

    public function __construct(

        #[Assert\NotBlank, Assert\Choice(['VISA', 'Mastercard'])]
        public readonly string $paymentBrand,
        #[Assert\NotBlank]
        public readonly string $cardNumber,
        #[Assert\NotBlank]
        public readonly string $cardHolder,
        #[Assert\NotBlank]
        public readonly string $cardExpiryMonth,
        #[Assert\NotBlank]
        public readonly string $cardExpiryYear,
        #[Assert\NotBlank]
        public readonly string $cardCvv


    ){}
}
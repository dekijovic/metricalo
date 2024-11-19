<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;
class PaymentDto
{

    public function __construct(

        #[Assert\NotBlank]
        public readonly int $amount,
        #[Assert\NotBlank, Assert\Choice(['USD', 'EUR'])]
        public readonly string $currency,
        public readonly? PaymentShiftDto $shift4,
        public readonly? PaymentAciDto $aci
    ){}
}
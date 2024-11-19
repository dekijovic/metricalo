<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;
class PaymentShiftDto
{

    public function __construct(

        #[Assert\NotBlank]
        public readonly? string $customer,
        #[Assert\NotBlank]
        public readonly? string $card,
    ){}
}
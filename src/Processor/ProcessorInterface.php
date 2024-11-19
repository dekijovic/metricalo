<?php

namespace App\Processor;

use App\Dto\PaymentAciDto;
use App\Dto\PaymentDto;

interface ProcessorInterface
{

    public function payment(PaymentDto $dto);
}
<?php

namespace App\Command;

use App\Dto\PaymentAciDto;
use App\Dto\PaymentDto;
use App\Dto\PaymentShiftDto;
use App\Processor\ProcessorLocator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'start:payment',
    description: 'Add a short description for your command',
)]
class PaymentCommand extends Command
{
    public function __construct(private readonly ProcessorLocator $processorLocator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg', InputArgument::REQUIRED, 'add Payment Processor')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg = $input->getArgument('arg');


        if (!in_array($arg, ['shift4', 'aci'])) {
            $io->error(sprintf('Only [shift4, aci] are acceptable arguments. You passed an argument: %s', $arg));
            return Command::FAILURE;
        }

        $processor = $this->processorLocator->handle($arg);

        $amountQ = new Question('Insert Amount');
        $amountQ->setValidator(function ($arg) {
            if (null == $arg) {
                throw new \RuntimeException('You didnt enter value');
            }
            return $arg;
        });
        $amount = $io->askQuestion($amountQ);
        //***************************************************/
        $currencyQ = new Question('Insert Currency');
        $currencyQ->setValidator(function ($arg) {
            if (null == $arg) {
                throw new \RuntimeException('You didnt enter value');
            }
            return $arg;
        });
        $currency = $io->askQuestion($currencyQ);
        //***************************************************/
        if($arg == 'shift4'){
            $customerQ = new Question('Insert Customer');
            $customerQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $customer = $io->askQuestion($customerQ);

            $cardQ = new Question('Insert Card');
            $cardQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $card = $io->askQuestion($cardQ);

            $shift4 = new PaymentShiftDto(customer: $customer, card: $card);
        }
        //***************************************************/
        if($arg == 'aci'){
            $cardQ = new Question('Insert Card Number');
            $cardQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $cardNumber = $io->askQuestion($cardQ);
            //***************************************************/
            $brandQ = new Question('Insert Payment Brand');
            $brandQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $brand = $io->askQuestion($brandQ);
            //***************************************************/
            $holderQ = new Question('Insert Card Holder');
            $holderQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $holder = $io->askQuestion($holderQ);
            //***************************************************/
            $monthQ = new Question('Insert Expiry Month');
            $monthQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $month = $io->askQuestion($monthQ);

            $yearQ = new Question('Insert Expiry Year');
            $yearQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $year = $io->askQuestion($yearQ);

            $cvvQ = new Question('Insert Cvv');
            $cvvQ->setValidator(function ($arg) {
                if (null == $arg) {
                    throw new \RuntimeException('You didnt enter value');
                }
                return $arg;
            });
            $cvv = $io->askQuestion($cvvQ);

            $aciDto = new PaymentAciDto(
                paymentBrand: $brand,
                cardNumber: $cardNumber,
                cardHolder: $holder,
                cardExpiryMonth: $month,
                cardExpiryYear: $year,
                cardCvv: $cvv
            );
        }

        $dto = new PaymentDto(
            amount: $amount,
            currency: $currency,
            shift4: $shift4 ?? null,
            aci: $aciDto ?? null
        );

        try {
            $processor->payment($dto);
        }catch (\Exception $e){
            $io->error("Payment Failed: ".$e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Payment Successful');

        return Command::SUCCESS;
    }
}

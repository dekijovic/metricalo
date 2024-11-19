<?php

namespace App\Controller;

use App\Dto\PaymentDto;
use App\Exception\ValidationException;
use App\Processor\ProcessorLocator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(
    path: '/action',
    name: '',
)]
class ActionController extends AbstractController
{

    public function __construct(
        private readonly ProcessorLocator $processorLocator,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ){
    }

    #[Route(
        path: '/{processor}',
        name: '',
        methods: [Request::METHOD_POST]
    )]
    public function index(Request $request, $processor): Response
    {
        $dto = $this->serializer->deserialize(
            json_encode($request->toArray()),
            PaymentDto::class,
            'json');
            $vaolations = $this->validator->validate($dto);
            if($vaolations->count()>0){
                $errors = [];
                foreach ($vaolations as $vaolation){
                    $errors[] = $vaolation->getMessage();
                }
                throw new ValidationException($errors);

            }

        $processor = $this->processorLocator->handle($processor);
        $processor->payment($dto);
        return new JsonResponse(['message' => "Payment Successful"]);
    }
}
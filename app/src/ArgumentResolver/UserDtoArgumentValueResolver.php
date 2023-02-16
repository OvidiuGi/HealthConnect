<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Dto\UserDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

class UserDtoArgumentValueResolver implements ValueResolverInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (UserDto::class !== $argument->getType()) {
            return;
        }

        $data = $request->getContent();

        yield $this->serializer->deserialize($data, UserDto::class, 'json');
    }
}
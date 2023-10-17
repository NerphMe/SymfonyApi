<?php

namespace App\Api\Model;

use App\Api\Interfaces\ResolvableModelInterface;
use App\Exception\ValidationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ModelResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();

        return null !== $type && class_exists($type) && is_subclass_of($type, ResolvableModelInterface::class);
    }

    /**
     * @throws ValidationFailedException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        if (null === $type) {
            throw new \InvalidArgumentException('Type cannot be null');
        }

        $object = $this->serializer->deserialize($request->getContent(), $type, 'json');

        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            $errorsResponse = $this->formatErrors($errors);
            throw new ValidationFailedException(json_encode($errorsResponse));
        }

        yield $object;
    }

    private function formatErrors(ConstraintViolationListInterface $errors): array
    {
        $errorsResponse = [];

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $propertyPath = $error->getPropertyPath();

            $errorsResponse[$propertyPath][] = $error->getMessage();
        }

        $structuredResponse = [];
        foreach ($errorsResponse as $field => $fieldErrors) {
            $structuredResponse[] = [
                'field' => $field,
                'errors' => $fieldErrors,
            ];
        }

        return $structuredResponse;
    }
}

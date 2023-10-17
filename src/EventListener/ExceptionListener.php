<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\HttpNotFoundException;
use App\Exception\ValidationFailedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        // Skip errors for 405
        if ($exception instanceof MethodNotAllowedHttpException) {
            return;
        }

        $response = new JsonResponse();

        // any exception or 500
        $response->setContent(json_encode(['error' => $exception->getMessage()]));
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);

        if ($exception instanceof ValidationFailedException || $exception instanceof HttpNotFoundException) {
            $errorData = json_decode($exception->getMessage(), true);

            $response->setContent(json_encode(['errors' => $errorData]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof NotFoundHttpException) {
            $response->setContent(json_encode(['errors' => 'Not found.']));
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}

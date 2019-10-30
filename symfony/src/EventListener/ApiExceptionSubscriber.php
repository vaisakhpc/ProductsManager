<?php

namespace App\EventListener;

use App\Model\Error;
use ErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * onKernelException
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        if ($e instanceof HttpException) {
            $exception = $this->mapException($e);
            $code = $e->getStatusCode();
        } elseif ($e instanceof ErrorException) {
            $exception = $this->mapErrorException($e);
            $code = $e->getCode() ?: 500;
        } else {
            $exception = new Error(
                $e->getCode() ?: 500,
                $e->getMessage()
            );
            $code = $e->getCode() ?: 500;
        }
        $response = new JsonResponse(
            ["error" => $exception->jsonSerialize()],
            $code
        );
        $response->headers->set('Content-Type', 'application/problem+json');

        $event->setResponse($response);
    }

    /**
     * getSubscribedEvents
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException',
        );
    }

    /**
     * @param HttpException $e
     * @return Error
     */
    private function mapException(HttpException $e): Error
    {
        return new Error(
            $e->getStatusCode(),
            $e->getMessage()
        );
    }

    /**
     * @param ErrorException $e
     * @return Error
     */
    private function mapErrorException(ErrorException $e): Error
    {
        return new Error(
            $e->getCode() ?: 500,
            $e->getMessage()
        );
    }
}

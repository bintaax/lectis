<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class AccessDeniedSubscriber implements EventSubscriberInterface
{
    public function __construct(private Environment $twig)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $exception = $event->getThrowable();

        if (
            !$exception instanceof AccessDeniedException
            && !$exception instanceof AccessDeniedHttpException
        ) {
            return;
        }

        $request = $event->getRequest();
        $preferredFormat = $request->getPreferredFormat();

        if (!in_array($preferredFormat, ['html', 'txt'], true)) {
            return;
        }

        $response = new Response(
            $this->twig->render('errors/403.html.twig'),
            Response::HTTP_FORBIDDEN
        );

        $event->setResponse($response);
    }
}

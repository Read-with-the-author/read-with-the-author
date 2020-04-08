<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Symfony\Component\Routing\RequestContextAwareInterface;

final class SetSchemeOnRequestContext implements EventSubscriberInterface
{
    private RequestContextAwareInterface $urlGenerator;

    private string $siteBaseScheme;

    public function __construct(RequestContextAwareInterface $urlGenerator, string $siteBaseScheme)
    {
        $this->urlGenerator = $urlGenerator;
        $this->siteBaseScheme = $siteBaseScheme;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 31]], // after Symfony's RouterListener
            KernelEvents::FINISH_REQUEST => [['onKernelRequest', -1]], // after Symfony's RouterListener
        ];
    }

    public function onKernelRequest(): void
    {
        $this->urlGenerator->getContext()->setScheme($this->siteBaseScheme);
    }
}

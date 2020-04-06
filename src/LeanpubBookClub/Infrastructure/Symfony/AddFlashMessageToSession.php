<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony;

use LeanpubBookClub\Application\ProducesFlashMessage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddFlashMessageToSession
{
    private SessionInterface $session;

    private TranslatorInterface $translator;

    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    public function notify(object $event): void
    {
        if (!$this->session instanceof Session) {
            return;
        }

        if (!$event instanceof ProducesFlashMessage) {
            return;
        }

        $this->session->getFlashBag()->add(
            $event->flashType(),
            $this->translator->trans($event->flashTranslatableMessage(), $event->flashTranslationVariables())
        );
    }
}

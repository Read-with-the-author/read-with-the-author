<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony;

use LeanpubBookClub\Application\Email\Email;
use LeanpubBookClub\Application\Email\Mailer;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class SymfonyMailer implements Mailer
{
    private MailerInterface $symfonyMailer;

    private TranslatorInterface $translator;

    private EmailAddress $systemEmailAddress;

    private Environment $twig;

    public function __construct(
        MailerInterface $symfonyMailer,
        TranslatorInterface $translator,
        EmailAddress $systemEmailAddress,
        Environment $twig
    ) {
        $this->symfonyMailer = $symfonyMailer;
        $this->translator = $translator;
        $this->systemEmailAddress = $systemEmailAddress;
        $this->twig = $twig;
    }

    public function send(Email $email): void
    {
        $message = (new TemplatedEmail())
            ->from($this->systemEmailAddress->asString())
            ->to($email->recipient())
            ->subject($this->translator->trans($email->subject()))
            ->htmlTemplate($email->template())
            ->context($email->templateVariables());

        $this->symfonyMailer->send($message);
    }
}

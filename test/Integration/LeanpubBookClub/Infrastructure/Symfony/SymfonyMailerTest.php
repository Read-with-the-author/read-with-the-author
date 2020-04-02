<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony;

use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use LeanpubBookClub\Application\Email\AccessTokenEmail;
use LeanpubBookClub\Application\Email\AttendeeRegisteredForSessionEmail;
use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use rpkamp\Mailhog\MailhogClient;
use rpkamp\Mailhog\Message\Contact;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Test\Acceptance\MemberBuilder;

/**
 * @group email
 */
final class SymfonyMailerTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_sends_the_access_token_email(): void
    {
        $accessToken = '96ee47ec-16fa-4e79-9049-f43aff093a9e';
        $email = new AccessTokenEmail(
            EmailAddress::fromString('matthias@matthiasnoback.nl'),
            AccessToken::fromString($accessToken)
        );

        $this->symfonyMailer()->send($email);

        $client = $this->mailhogClient();
        $message = $client->getLastMessage();

        self::assertEquals('Your access token', $message->subject);
        self::assertStringContainsString(
            '/member-area/login?token=' . $accessToken,
            $message->body
        );
        self::assertTrue($message->recipients->contains(new Contact($email->recipient())));
    }

    /**
     * @test
     * @group wip
     */
    public function it_sends_the_attendee_registered_for_session_email(): void
    {
        $member = MemberBuilder::create()->build();

        $email = new AttendeeRegisteredForSessionEmail($member);

        $this->symfonyMailer()->send($email);

        $client = $this->mailhogClient();
        $message = $client->getLastMessage();

        self::assertTrue($message->recipients->contains(new Contact($email->recipient())));

        // @todo add more assertions
        // @todo add calendar links
        // @todo add ics downloadable file
    }

    private function symfonyMailer(): SymfonyMailer
    {
        self::bootKernel();

        $mailer = self::$container->get(SymfonyMailer::class);
        self::assertInstanceOf(SymfonyMailer::class, $mailer);
        /** @var SymfonyMailer $mailer */

        return $mailer;
    }

    private function mailhogClient(): MailhogClient
    {
        return $client = new MailhogClient(
            GuzzleAdapter::createWithConfig([]),
            new GuzzleMessageFactory(),
            'http://mailhog:8025/'
        );
    }
}

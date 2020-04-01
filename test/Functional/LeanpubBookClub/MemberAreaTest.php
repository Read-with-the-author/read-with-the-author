<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use LeanpubBookClub\Application\AttendSession;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSession;
use LeanpubBookClub\Application\UpdateTimeZone;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use Symfony\Component\DomCrawler\Crawler;

final class MemberAreaTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->logInMember($this->memberId, $this->memberTimeZone);
    }

    public function testUpcomingEvents(): void
    {
        $upcomingSessions = [
            new UpcomingSession(
                'e44c5dfa-73f5-4355-aba7-21ac67c3c87a',
                '2020-02-01 20:00',
                'Chapter 1',
                true
            ),
            new UpcomingSession(
                '336ca07e-b3b8-47c7-a52f-7b67b6f16e49',
                '2020-02-08 20:00',
                'Chapter 2',
                false
            )
        ];

        $this->upcomingSessionsAre($this->memberId, $upcomingSessions);

        $crawler = $this->client->request('GET', '/member-area/');

        self::assertTrue($this->client->getResponse()->isSuccessful());
        self::assertStringContainsString('Upcoming sessions', $crawler->filter('h2')->text());

        $this->assertResponseContainsUpcomingSessions($crawler, $upcomingSessions);

        self::assertMemberIsRegisteredAsAttendeeForSession($crawler, 'e44c5dfa-73f5-4355-aba7-21ac67c3c87a');
    }

    public function testAttendSession(): void
    {
        $this->upcomingSessionsAre($this->memberId, [
            new UpcomingSession(
                '336ca07e-b3b8-47c7-a52f-7b67b6f16e49',
                '2020-02-08 20:00',
                'Chapter 2',
                false
            )
        ]);

        $this->client->request('GET', '/member-area/');

        $this->application->expects($this->once())
            ->method('attendSession')
            ->with(new AttendSession('336ca07e-b3b8-47c7-a52f-7b67b6f16e49', $this->memberId));

        $this->client->followRedirects(false);
        $this->client->submitForm('Attend this session');

        self::assertTrue($this->client->getResponse()->isRedirect('/member-area/'));
    }

    /**
     * @group wip
     */
    public function testUpdateTimeZone(): void
    {
        $this->client->request('GET', '/member-area/');

        $newTimeZone = 'America/New_York';

        $this->application->expects($this->once())
            ->method('updateTimeZone')
            ->with(new UpdateTimeZone($this->memberId, $newTimeZone));

        $this->client->submitForm('Update time zone', [
            'update_time_zone_form[timeZone]' => $newTimeZone
        ]);
    }

    /**
     * @param array<UpcomingSession> $upcomingSessions
     */
    private function upcomingSessionsAre(string $memberId, array $upcomingSessions): void
    {
        // @todo let listUpcomingSessions accept a string argument
        $this->application->expects($this->any())
            ->method('listUpcomingSessions')
            ->with(LeanpubInvoiceId::fromString($memberId))
            ->willReturn($upcomingSessions);
    }

    /**
     * @param array<UpcomingSession> $upcomingSessions
     */
    private function assertResponseContainsUpcomingSessions(Crawler $crawler, array $upcomingSessions)
    {
        foreach ($upcomingSessions as $upcomingSession) {
            /** @var UpcomingSession $upcomingSession */
            $sessionId = $upcomingSession->sessionId();
            $sessionElement = self::sessionElement($crawler, $sessionId);

            self::assertEquals($upcomingSession->description(), $sessionElement->filter('.session-description')->text());
            self::assertEquals($upcomingSession->date($this->memberTimeZone), $sessionElement->filter('.session-date')->text());
            self::assertEquals($upcomingSession->time($this->memberTimeZone), $sessionElement->filter('.session-time')->text());
        }
    }

    private static function assertMemberIsRegisteredAsAttendeeForSession(Crawler $crawler, string $sessionId): void
    {
        $sessionElement = self::sessionElement($crawler, $sessionId);

        self::assertStringContainsString('table-success', $sessionElement->first()->attr('class'));
        self::assertStringContainsString('Attending', $sessionElement->filter('.session-actions')->text());
    }

    private static function sessionElement(Crawler $crawler, string $sessionId): Crawler
    {
        return $crawler->filter('.session-' . $sessionId);
    }
}

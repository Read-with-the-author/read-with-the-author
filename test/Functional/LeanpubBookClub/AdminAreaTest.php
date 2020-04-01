<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use LeanpubBookClub\Application\PlanSession;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSessionForAdministrator;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

final class AdminAreaTest extends WebTestCase
{
    public function testAccessDeniedForNonLoggedInUsers(): void
    {
        $this->client->request('GET', '/admin-area/');

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListUpcomingSessions(): void
    {
        $upcomingSessions = [
            (new UpcomingSessionForAdministrator(
                'e44c5dfa-73f5-4355-aba7-21ac67c3c87a',
                '2020-02-01 20:00',
                'Chapter 1',
                10
            ))->withNumberOfAttendees(5),
            (new UpcomingSessionForAdministrator(
                '336ca07e-b3b8-47c7-a52f-7b67b6f16e49',
                '2020-02-08 20:00',
                'Chapter 2',
                20
            ))->withNumberOfAttendees(18)
        ];

        $this->application->expects($this->any())
            ->method('listUpcomingSessionsForAdministrator')
            ->willReturn($upcomingSessions);

        $crawler = $this->client->request('GET', '/admin-area/', [], [], $this->serverVariables());

        self::assertStringContainsString('Upcoming sessions', $crawler->text());

        $session1 = $upcomingSessions[0];
        /** @var UpcomingSessionForAdministrator $session1 */
        self::assertResponseContainsUpcomingSession(
            $crawler,
            $session1->sessionId(),
            'Saturday, February 1st',
            '21:00',
            $session1->description(),
            '5/10'
        );

        $session2 = $upcomingSessions[1];
        /** @var UpcomingSessionForAdministrator $session2 */
        self::assertResponseContainsUpcomingSession(
            $crawler,
            $session2->sessionId(),
            'Saturday, February 8th',
            '21:00',
            $session2->description(),
            '18/20'
        );
    }

    private static function assertResponseContainsUpcomingSession(
        Crawler $crawler,
        string $sessionId,
        string $expectedDate,
        string $expectedTime,
        string $expectedDescription,
        string $expectedAttendees
    ): void {
        $sessionElement = $crawler->filter('.session-' . $sessionId);

        self::assertEquals($expectedDate, $sessionElement->filter('.session-date')->text());
        self::assertEquals($expectedTime, $sessionElement->filter('.session-time')->text());
        self::assertEquals($expectedDescription, $sessionElement->filter('.session-description')->text());
        self::assertEquals($expectedAttendees, $sessionElement->filter('.session-attendees')->text());
    }

    public function testPlanSession(): void
    {
        $this->client->request('GET', '/admin-area/', [], [], $this->serverVariables());

        $this->application->expects($this->once())
            ->method('planSession')
            ->with(
                new PlanSession(
                    '2020-03-30 20:00',
                    'Europe/Amsterdam',
                    'Description',
                    10
                )
            )
            ->willReturn('38a88229-70b6-458c-83e9-77703ca4cca0');

        $this->client->followRedirects(false);
        $this->client->submitForm(
            'Plan session',
            [
                'plan_session_form[date][date][month]' => '3',
                'plan_session_form[date][date][day]' => '30',
                'plan_session_form[date][date][year]' => '2020',
                'plan_session_form[date][time][hour]' => '20',
                'plan_session_form[date][time][minute]' => '0',
                'plan_session_form[description]' => 'Description',
                'plan_session_form[maximumNumberOfParticipants]' => '10'
            ]
        );

        self::assertTrue($this->client->getResponse()->isRedirect('/admin-area/'));
    }

    /**
     * @return array<string,string>
     */
    protected function serverVariables(): array
    {
        return [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'test'
        ];
    }
}

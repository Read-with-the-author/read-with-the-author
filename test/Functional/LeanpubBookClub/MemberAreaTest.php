<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use LeanpubBookClub\Application\AttendSession;
use LeanpubBookClub\Application\CancelAttendance;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\SessionCall\CouldNotGetCallUrl;
use LeanpubBookClub\Application\UpcomingSessions\SessionForMember;
use LeanpubBookClub\Application\UpdateTimeZone;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Test\Acceptance\MemberBuilder;

final class MemberAreaTest extends WebTestCase
{
    private Member $loggedInMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logInMember(
            MemberBuilder::create()->build()
        );
    }

    public function testRedirectToHomepageWhenAccessingMemberAreaWithIncorrectToken(): void
    {
        $this->client->followRedirects(false);

        // login with unknown access token
        $accessToken = '0a56900e-fc10-4fde-b63c-a17ebc3d5002';

        $this->application->expects($this->any())
            ->method('getOneMemberByAccessToken')
            ->with($accessToken)
            ->willThrowException(new CouldNotFindMember());

        $this->client->request('GET', '/member-area/login', ['token' => $accessToken]);

        self::assertTrue($this->client->getResponse()->isRedirect('http://localhost/'));

        $crawler = $this->client->followRedirect();

        self::assertResponseHasFlashOfType($crawler, FlashType::WARNING, 'Authentication failed');
    }

    public function testRedirectToHomepageWhenAccessingMemberAreaWithoutAToken(): void
    {
        $this->client->followRedirects(false);

        $this->client->request('GET', '/member-area/login');

        self::assertTrue($this->client->getResponse()->isRedirect('/'));
    }

    public function testLoginWithAccessToken(): void
    {
        $accessToken = '048c4168-8a3c-4857-b78e-adafa12069b4';

        $member = MemberBuilder::create()->build();
        $this->memberExists($member);
        $this->accessTokenIsValidForMember($accessToken, $member);

        $crawler = $this->client->request('GET', '/member-area/login', ['token' => $accessToken]);

        self::assertTrue($this->client->getResponse()->isSuccessful());

        self::assertStringContainsString(
            $member->memberId()->asString(),
            $crawler->filter('.logged_in_username')->text()
        );
    }

    private function accessTokenIsValidForMember(string $accessToken, Member $member): void
    {
        $this->application->expects($this->any())
            ->method('getOneMemberByAccessToken')
            ->with($accessToken)
            ->willReturn($member);
    }

    public function testUpcomingEvents(): void
    {
        $upcomingSessions = [
            (new SessionForMember(
                'e44c5dfa-73f5-4355-aba7-21ac67c3c87a',
                '2020-02-01 20:00',
                'Chapter 1'
            ))->withActiveMemberRegisteredAsAttendee(true),
            (new SessionForMember(
                '336ca07e-b3b8-47c7-a52f-7b67b6f16e49',
                '2020-02-08 20:00',
                'Chapter 2'
            ))->withActiveMemberRegisteredAsAttendee(false)
        ];

        $this->upcomingSessionsAre($this->loggedInMember, $upcomingSessions);

        $crawler = $this->client->request('GET', '/member-area/');

        self::assertTrue($this->client->getResponse()->isSuccessful());
        self::assertStringContainsString('Upcoming sessions', $crawler->filter('h2')->text());

        $this->assertResponseContainsUpcomingSessions($crawler, $upcomingSessions);

        self::assertMemberIsRegisteredAsAttendeeForSession($crawler, 'e44c5dfa-73f5-4355-aba7-21ac67c3c87a');
        self::assertMemberIsNotRegisteredAsAttendeeForSession($crawler, '336ca07e-b3b8-47c7-a52f-7b67b6f16e49');
    }

    public function testAttendSession(): void
    {
        $this->upcomingSessionsAre(
            $this->loggedInMember,
            [
                new SessionForMember(
                    '336ca07e-b3b8-47c7-a52f-7b67b6f16e49',
                    '2020-02-08 20:00',
                    'Chapter 2'
                )
            ]);

        $this->client->request('GET', '/member-area/');

        $this->application->expects($this->once())
            ->method('attendSession')
            ->with(
                new AttendSession('336ca07e-b3b8-47c7-a52f-7b67b6f16e49', $this->loggedInMember->memberId()->asString())
            );

        $this->client->followRedirects(false);
        $this->client->submitForm('Attend this session');

        self::assertTrue($this->client->getResponse()->isRedirect('/member-area/'));
    }

    public function testCancelAttendance(): void
    {
        $this->upcomingSessionsAre(
            $this->loggedInMember,
            [
                (new SessionForMember(
                    '336ca07e-b3b8-47c7-a52f-7b67b6f16e49',
                    '2020-02-08 20:00',
                    'Chapter 2'
                ))->withActiveMemberRegisteredAsAttendee(true)
            ]);

        $this->client->request('GET', '/member-area/');

        $this->application->expects($this->once())
            ->method('cancelAttendance')
            ->with(
                new CancelAttendance('336ca07e-b3b8-47c7-a52f-7b67b6f16e49', $this->loggedInMember->memberId()->asString())
            );

        $this->client->followRedirects(false);
        $this->client->submitForm('Cancel attendance');

        self::assertTrue($this->client->getResponse()->isRedirect('/member-area/'));
    }

    public function testUpdateTimeZone(): void
    {
        $this->client->request('GET', '/member-area/');

        $newTimeZone = 'America/New_York';

        $this->application->expects($this->once())
            ->method('updateTimeZone')
            ->with(new UpdateTimeZone($this->loggedInMember->memberId()->asString(), $newTimeZone));

        $this->client->submitForm(
            'Update time zone',
            [
                'update_time_zone_form[timeZone]' => $newTimeZone
            ]);
    }

    public function testRedirectToVideoCall(): void
    {
        $this->client->request('GET', '/member-area/');

        $sessionId = '336ca07e-b3b8-47c7-a52f-7b67b6f16e49';
        $callUrl = 'https://whereby.com/matthiasnoback';

        $this->application->expects($this->any())
            ->method('getCallUrlForSession')
            ->with($sessionId)
            ->willReturn($callUrl);

        $this->client->followRedirects(false);
        $this->client->request('GET', sprintf('/member-area/redirect-to-video-call/%s', $sessionId));

        self::assertTrue($this->client->getResponse()->isRedirect($callUrl));
    }

    public function testCallUrlNotAvailableYet(): void
    {
        $this->client->request('GET', '/member-area/');

        $sessionId = '336ca07e-b3b8-47c7-a52f-7b67b6f16e49';

        $this->application->expects($this->any())
            ->method('getCallUrlForSession')
            ->with($sessionId)
            ->willThrowException(new CouldNotGetCallUrl());

        $this->client->followRedirects(false);
        $this->client->request('GET', sprintf('/member-area/redirect-to-video-call/%s', $sessionId));

        self::assertTrue($this->client->getResponse()->isRedirect('/member-area/'));

        $crawler = $this->client->followRedirect();

        self::assertResponseHasFlashOfType($crawler, FlashType::WARNING, 'not available');
    }

    /**
     * @param array<SessionForMember> $upcomingSessions
     */
    private function upcomingSessionsAre(Member $member, array $upcomingSessions): void
    {
        $this->application->expects($this->any())
            ->method('listUpcomingSessionsForMember')
            ->with($member->memberId()->asString())
            ->willReturn($upcomingSessions);
    }

    /**
     * @param array<SessionForMember> $upcomingSessions
     */
    private function assertResponseContainsUpcomingSessions(Crawler $crawler, array $upcomingSessions)
    {
        foreach ($upcomingSessions as $upcomingSession) {
            /** @var SessionForMember $upcomingSession */
            $sessionId = $upcomingSession->sessionId();
            $sessionElement = self::sessionElement($crawler, $sessionId);

            self::assertEquals(
                $upcomingSession->description(),
                $sessionElement->filter('.session-description')->text());
            self::assertEquals(
                $upcomingSession->date($this->loggedInMember->timeZone()),
                $sessionElement->filter('.session-date')->text());
            self::assertEquals(
                $upcomingSession->time($this->loggedInMember->timeZone()),
                $sessionElement->filter('.session-time')->text());
        }
    }

    private static function assertMemberIsRegisteredAsAttendeeForSession(Crawler $crawler, string $sessionId): void
    {
        $sessionElement = self::sessionElement($crawler, $sessionId);

        self::assertStringContainsString('table-success', $sessionElement->first()->attr('class'));
        self::assertStringContainsString('Cancel attendance', $sessionElement->filter('.session-actions')->text());
    }

    private static function assertMemberIsNotRegisteredAsAttendeeForSession(Crawler $crawler, string $sessionId): void
    {
        $sessionElement = self::sessionElement($crawler, $sessionId);

        self::assertStringNotContainsString('table-success', $sessionElement->first()->attr('class'));
        self::assertStringContainsString('Attend this session', $sessionElement->filter('.session-actions')->text());
    }

    private static function sessionElement(Crawler $crawler, string $sessionId): Crawler
    {
        return $crawler->filter('.session-' . $sessionId);
    }

    private function logInMember(Member $member): void
    {
        $this->memberExists($member);

        $session = self::$container->get('session');

        $firewallName = 'member_area';
        $firewallContext = $firewallName;

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new PostAuthenticationGuardToken($member, $firewallName, ['ROLE_MEMBER']);
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function memberExists(Member $member): void
    {
        $this->application->expects($this->any())
            ->method('getOneMemberById')
            ->with($member->memberId()->asString())
            ->willReturn($member);

        $this->loggedInMember = $member;
    }
}

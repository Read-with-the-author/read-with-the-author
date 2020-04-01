<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use LeanpubBookClub\Application\PlanSession;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use Symfony\Component\HttpFoundation\Response;

final class AdminAreaTest extends WebTestCase
{
    public function testAccessDeniedForNonLoggedInUsers(): void
    {
        $this->client->request('GET', '/admin-area/');

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
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
            ->willReturn(SessionId::fromString('38a88229-70b6-458c-83e9-77703ca4cca0'));

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

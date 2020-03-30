<?php
declare(strict_types=1);

namespace LeanpubBookClub;

final class MemberAreaTest extends WebTestCase
{
    public function testUpcomingEvents(): void
    {
        $this->logInMember();

        $crawler = $this->client->request('GET', '/member-area/');

        self::assertTrue($this->client->getResponse()->isSuccessful());
        self::assertStringContainsString('Upcoming events', $crawler->filter('h2')->text());
    }
}

<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class IndexTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

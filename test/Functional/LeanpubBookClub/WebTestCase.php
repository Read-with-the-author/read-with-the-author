<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Infrastructure\ProductionServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

abstract class WebTestCase extends SymfonyWebTestCase
{
    /**
     * @var ApplicationInterface & MockObject
     */
    protected $application;

    protected KernelBrowser $client;

    protected string $memberId = 'jP6LfQ3UkfOvZTLZLNfDfg';
    protected string $memberTimeZone = 'America/New_York';

    protected function setUp(): void
    {
        $this->client = $this->createClientWithMockedApplication();
    }

    protected static function assertResponseHasFlashOfType(
        Crawler $crawler,
        string $type,
        string $messageContains
    ) {
        $nodes = $crawler->filter('.alerts .alert-' . $type);
        self::assertGreaterThan(0, count($nodes), 'Did not find a flash message of type ' . $type);

        self::assertStringContainsString(
            $messageContains,
            $nodes->text()
        );
    }

    protected function createClientWithMockedApplication(): KernelBrowser
    {
        $client = WebTestCase::createClient();
        $client->disableReboot();
        $client->followRedirects();

        $this->application = $this->createMock(ApplicationInterface::class);
        $this->setApplication($client, $this->application);

        return $client;
    }

    private function setApplication(KernelBrowser $client, ApplicationInterface $application): void
    {
        $container = $client->getContainer();
        Assert::that($container)->isInstanceOf(ContainerInterface::class);
        /** @var ContainerInterface $container */

        $serviceContainer = $container->get(ProductionServiceContainer::class);
        Assert::that($serviceContainer)->isInstanceOf(ProductionServiceContainer::class);
        /** @var ProductionServiceContainer $serviceContainer */

        $serviceContainer->setApplication($application);
    }

    protected function logInMember(string $memberId, string $timeZone = 'Europe/Amsterdam'): void
    {
        $session = self::$container->get('session');

        $firewallName = 'member_area';
        $firewallContext = $firewallName;

        $member = new Member($memberId, $timeZone);

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new PostAuthenticationGuardToken($member, $firewallName, ['ROLE_MEMBER']);
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->memberExists($memberId, $timeZone);
    }

    protected function memberExists(string $memberId, string $timeZone = 'Europe/Amsterdam'): void
    {
        $this->application->expects($this->any())
            ->method('getOneById')
            ->with($memberId)
            ->willReturn(new Member($memberId, $timeZone));
    }

    protected function dumpResponse(): void
    {
        dump($this->client->getResponse());
    }
}

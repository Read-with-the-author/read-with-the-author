<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use LeanpubBookClub\Infrastructure\ProductionServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

final class IndexTest extends WebTestCase
{
    /**
     * @var ApplicationInterface & MockObject
     */
    private $application;

    private static function assertResponseHasFlashOfType(
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

    public function testIndex(): void
    {
        $client = static::createClient();
        $applicationMock = $this->createMock(ApplicationInterface::class);
        $this->setApplication($client, $applicationMock);

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRequestAccess(): void
    {
        $client = $this->createClientWithMockedApplication();

        $emailAddress = 'info@matthiasnoback.nl';
        $leanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';

        $this->application
            ->expects($this->once())
            ->method('requestAccess')
            ->with(new RequestAccess($leanpubInvoiceId, $emailAddress));

        $client->request('GET', '/');

        $client->submitForm(
            'Request access',
            [
                'request_access_form[leanpubInvoiceId]' => $leanpubInvoiceId,
                'request_access_form[emailAddress]' => $emailAddress
            ]
        );
    }

    public function testRequestAccessToken(): void
    {
        $client = $this->createClientWithMockedApplication();

        $leanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';

        $this->application
            ->expects($this->once())
            ->method('generateAccessToken')
            ->with($leanpubInvoiceId);

        $client->request('GET', '/');

        $client->submitForm(
            'Get an access token',
            [
                'request_access_token_form[leanpubInvoiceId]' => $leanpubInvoiceId
            ]
        );
    }

    public function testLoginWithAccessToken(): void
    {
        $client = $this->createClientWithMockedApplication();

        $accessToken = '048c4168-8a3c-4857-b78e-adafa12069b4';

        $memberId = 'jP6LfQ3UkfOvZTLZLNfDfg';
        $member = new Member($memberId);

        $this->application->expects($this->any())
            ->method('getOneByAccessToken')
            ->with($accessToken)
            ->willReturn($member);

        $this->application->expects($this->any())
            ->method('getOneById')
            ->with($memberId)
            ->willReturn($member);

        $crawler = $client->request('GET', '/login', ['token' => $accessToken]);

        self::assertTrue($client->getResponse()->isSuccessful());

        self::assertStringContainsString(
            $memberId,
            $crawler->filter('.logged_in_username')->text()
        );
    }

    public function testRedirectToHomepageWhenAccessingMemberAreaWithIncorrectToken(): void
    {
        $client = $this->createClientWithMockedApplication();
        $client->followRedirects(false);

        // login with unknown access token
        $accessToken = '0a56900e-fc10-4fde-b63c-a17ebc3d5002';

        $this->application->expects($this->any())
            ->method('getOneByAccessToken')
            ->with($accessToken)
            ->willThrowException(new CouldNotFindMember());

        $client->request('GET', '/login', ['token' => $accessToken]);

        self::assertTrue($client->getResponse()->isRedirect('http://localhost/'));

        $crawler = $client->followRedirect();

        self::assertResponseHasFlashOfType($crawler, FlashType::WARNING, 'Authentication failed');
    }

    /**
     * @group wip
     */
    public function testRedirectToHomepageWhenAccessingMemberAreaWithoutAToken(): void
    {
        $client = $this->createClientWithMockedApplication();
        $client->followRedirects(false);

        $client->request('GET', '/login');

        self::assertTrue($client->getResponse()->isRedirect('/'));
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

    private function createClientWithMockedApplication(): KernelBrowser
    {
        $client = self::createClient();
        $client->disableReboot();
        $client->followRedirects();

        $this->application = $this->createMock(ApplicationInterface::class);
        $this->setApplication($client, $this->application);

        return $client;
    }
}

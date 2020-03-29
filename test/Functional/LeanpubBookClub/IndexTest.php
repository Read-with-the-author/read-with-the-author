<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Infrastructure\ProductionServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class IndexTest extends WebTestCase
{
    /**
     * @var ApplicationInterface & MockObject
     */
    private $application;

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

        $crawler = $client->submitForm(
            'Get an access token',
            [
                'request_access_token_form[leanpubInvoiceId]' => $leanpubInvoiceId
            ]
        );

        self::assertStringContainsString('A new access token was sent to you by email', $crawler->filter('div.alerts')->text());
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

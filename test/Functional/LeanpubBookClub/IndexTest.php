<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\RequestAccess;
use LeanpubBookClub\Infrastructure\ProductionServiceContainer;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class IndexTest extends WebTestCase
{
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
        $client = static::createClient();
        $client->disableReboot();

        $emailAddress = 'info@matthiasnoback.nl';
        $leanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';

        $applicationMock = $this->createMock(ApplicationInterface::class);
        $applicationMock
            ->expects($this->once())
            ->method('requestAccess')
            ->with(new RequestAccess($leanpubInvoiceId, $emailAddress));

        $this->setApplication($client, $applicationMock);

        $client->request('GET', '/');

        $this->setApplication($client, $applicationMock);

        $client->submitForm(
            'Request access',
            [
                'request_access_form[leanpubInvoiceId]' => $leanpubInvoiceId,
                'request_access_form[emailAddress]' => $emailAddress
            ]
        );
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
}

<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\Members\Member;
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

        $client->submitForm(
            'Get an access token',
            [
                'request_access_token_form[leanpubInvoiceId]' => $leanpubInvoiceId
            ]
        );
    }

    /**
     * @group wip
     */
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


    public function testAccessDeniedWhenAccessingMemberAreaWithoutToken(): void
    {
        // @todo
        $this->markTestIncomplete();
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
        $client->catchExceptions(false);

        $this->application = $this->createMock(ApplicationInterface::class);
        $this->setApplication($client, $this->application);

        return $client;
    }
}

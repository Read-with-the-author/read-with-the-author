<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;

final class IndexTest extends WebTestCase
{
    public function testIndex(): void
    {
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRequestAccess(): void
    {
        $emailAddress = 'info@matthiasnoback.nl';
        $leanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';

        $this->application
            ->expects($this->once())
            ->method('requestAccess')
            ->with(new RequestAccess($leanpubInvoiceId, $emailAddress));

        $this->client->request('GET', '/');

        $this->client->submitForm(
            'Request access',
            [
                'request_access_form[leanpubInvoiceId]' => $leanpubInvoiceId,
                'request_access_form[emailAddress]' => $emailAddress
            ]
        );
    }

    public function testRequestAccessToken(): void
    {
        $leanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';

        $this->application
            ->expects($this->once())
            ->method('generateAccessToken')
            ->with($leanpubInvoiceId);

        $this->client->request('GET', '/');

        $this->client->submitForm(
            'Get an access token',
            [
                'request_access_token_form[leanpubInvoiceId]' => $leanpubInvoiceId
            ]
        );
    }

    public function testLoginWithAccessToken(): void
    {
        $accessToken = '048c4168-8a3c-4857-b78e-adafa12069b4';

        $memberId = 'jP6LfQ3UkfOvZTLZLNfDfg';
        $this->memberExists($memberId);
        $this->accessTokenIsValidForMember($accessToken, $memberId);

        $crawler = $this->client->request('GET', '/login', ['token' => $accessToken]);

        self::assertTrue($this->client->getResponse()->isSuccessful());

        self::assertStringContainsString(
            $memberId,
            $crawler->filter('.logged_in_username')->text()
        );
    }

    public function testRedirectToHomepageWhenAccessingMemberAreaWithIncorrectToken(): void
    {
        $this->client->followRedirects(false);

        // login with unknown access token
        $accessToken = '0a56900e-fc10-4fde-b63c-a17ebc3d5002';

        $this->application->expects($this->any())
            ->method('getOneByAccessToken')
            ->with($accessToken)
            ->willThrowException(new CouldNotFindMember());

        $this->client->request('GET', '/login', ['token' => $accessToken]);

        self::assertTrue($this->client->getResponse()->isRedirect('http://localhost/'));

        $crawler = $this->client->followRedirect();

        self::assertResponseHasFlashOfType($crawler, FlashType::WARNING, 'Authentication failed');
    }

    public function testRedirectToHomepageWhenAccessingMemberAreaWithoutAToken(): void
    {
        $this->client->followRedirects(false);

        $this->client->request('GET', '/login');

        self::assertTrue($this->client->getResponse()->isRedirect('/'));
    }

    private function accessTokenIsValidForMember(string $accessToken, string $memberId): void
    {
        $this->application->expects($this->any())
            ->method('getOneByAccessToken')
            ->with($accessToken)
            ->willReturn($this->application->getOneById($memberId));
    }
}

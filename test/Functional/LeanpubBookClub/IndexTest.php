<?php
declare(strict_types=1);

namespace LeanpubBookClub;

use LeanpubBookClub\Application\RequestAccess\RequestAccess;

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
        $timeZone = 'Europe/Amsterdam';

        $this->client->request('GET', '/');

        $this->application
            ->expects($this->once())
            ->method('requestAccess')
            ->with(new RequestAccess($leanpubInvoiceId, $emailAddress, $timeZone));

        $this->client->submitForm(
            'Request access',
            [
                'request_access_form[leanpubInvoiceId]' => $leanpubInvoiceId,
                'request_access_form[emailAddress]' => $emailAddress,
                'request_access_form[timeZone]' => $timeZone
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
}

<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\Email\AccessTokenEmail;
use LeanpubBookClub\Application\ImportPurchase;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Domain\Model\Member\AccessWasGrantedToMember;
use LeanpubBookClub\Domain\Model\Member\EmailAddress;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use PHPUnit\Framework\Assert;
use RuntimeException;

final class RegistrationContext extends FeatureContext
{
    private ?string $buyerLeanpubInvoiceId;

    private string $buyerEmailAddress = 'info@matthiasnoback.nl';


    /**
     * @Given someone has bought a copy of the book and the invoice ID was :invoiceId
     */
    public function someoneHasBoughtACopyOfTheBook(string $invoiceId): void
    {
        $this->application()->importPurchase(new ImportPurchase($invoiceId));

        $this->buyerLeanpubInvoiceId = $invoiceId;
    }

    /**
     * @When they request access to the club providing the same invoice ID
     */
    public function theySignUpForTheClubProvidingTheCorrectInvoiceID(): void
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);

        $this->application()->requestAccess(
            new RequestAccess($this->buyerLeanpubInvoiceId, $this->buyerEmailAddress)
        );
    }

    /**
     * @When someone requests access to the club providing the correct invoice ID
     */
    public function someoneToTheClubProvidingTheCorrectInvoiceId(): void
    {
        $invoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';
        $this->application()->importPurchase(new ImportPurchase($invoiceId));
        $this->application()->requestAccess(
            new RequestAccess($invoiceId, $this->buyerEmailAddress)
        );
    }

    /**
     * @When they request a new access token
     */
    public function whenTheyRequestNewAccessToken(): void
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);

        $this->application()->generateAccessToken(LeanpubInvoiceId::fromString($this->buyerLeanpubInvoiceId));
    }

    /**
     * @Then they should receive an email with an access token for their dashboard page
     */
    public function thenTheyShouldReceiveAnEmailWithAnAccessToken(): void
    {
        foreach ($this->serviceContainer()->mailer()->sentEmails() as $email) {
            if ($email instanceof AccessTokenEmail) {
                Assert::assertEquals(EmailAddress::fromString($this->buyerEmailAddress), $email->recipient());
                return;
            }
        }

        throw new RuntimeException('Received no such email');
    }

    /**
     * @Then they should be granted access to the club
     */
    public function theyShouldBeGrantedAccessToTheClub(): void
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);

        foreach ($this->dispatchedEvents() as $event) {
            if ($event instanceof AccessWasGrantedToMember
                && $event->leanpubInvoiceId()->equals(LeanpubInvoiceId::fromString($this->buyerLeanpubInvoiceId))) {
                return;
            }
        }

        throw new RuntimeException('Expected an AccessGrantedToMember event for the member who requested access');
    }

    /**
     * @When someone requests access to the club providing an invoice ID that does not match an actual purchase
     */
    public function someoneRequestsAccessToTheClubProvidingAnInvoiceIDThatDoesNotMatchAnActualPurchase(): void
    {
        $this->buyerLeanpubInvoiceId = '6gbXPEDMOEMKCNwOykPvpg';

        $this->application()->requestAccess(
            new RequestAccess(
                $unknownInvoiceId = $this->buyerLeanpubInvoiceId, $this->buyerEmailAddress
            )
        );
    }

    /**
     * @Then they should not be granted access to the club
     */
    public function theyShouldNotBeGrantedAccessToTheClub(): void
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);

        foreach ($this->dispatchedEvents() as $event) {
            if ($event instanceof AccessWasGrantedToMember && $event->leanpubInvoiceId()->equals(
                    LeanpubInvoiceId::fromString($this->buyerLeanpubInvoiceId))) {
                throw new RuntimeException('We did not expect an AccessGrantedToMember event to have been dispatched');
            }
        }
    }

    /**
     * @Given someone has been granted access to the club
     */
    public function someoneHasBeenGrantedAccessToTheClub(): void
    {
        $this->buyerLeanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';
        $this->application()->importPurchase(new ImportPurchase($this->buyerLeanpubInvoiceId));
        $this->application()->requestAccess(new RequestAccess($this->buyerLeanpubInvoiceId, $this->buyerEmailAddress));
    }

    /**
     * @When someone else requests access providing the same invoice ID
     */
    public function someoneElseRequestsAccessProvidingTheSameInvoiceID(): void
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);

        $this->serviceContainer()->eventDispatcherSpy()->clearEvents();

        $this->application()->requestAccess(
            new RequestAccess($this->buyerLeanpubInvoiceId, 'someoneelse@matthiasnoback.nl')
        );
    }
}

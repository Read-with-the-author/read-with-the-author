<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\ImportPurchase;
use LeanpubBookClub\Application\RequestAccess;
use LeanpubBookClub\Domain\Model\Member\AccessGrantedToMember;
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
            new RequestAccess($this->buyerEmailAddress, $this->buyerLeanpubInvoiceId)
        );
    }

    /**
     * @Then they should be granted access to the club
     */
    public function theyShouldBeGrantedAccessToTheClub(): void
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);

        foreach ($this->dispatchedEvents() as $event) {
            if ($event instanceof AccessGrantedToMember
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
                $this->buyerEmailAddress,
                $unknownInvoiceId = $this->buyerLeanpubInvoiceId
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
            if ($event instanceof AccessGrantedToMember && $event->leanpubInvoiceId()->equals(
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
        $this->application()->requestAccess(new RequestAccess($this->buyerEmailAddress, $this->buyerLeanpubInvoiceId));
    }

    /**
     * @When someone else requests access providing the same invoice ID
     */
    public function someoneElseRequestsAccessProvidingTheSameInvoiceID(): void
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);
        
        $this->serviceContainer()->eventDispatcherSpy()->clearEvents();

        $this->application()->requestAccess(
            new RequestAccess('someoneelse@matthiasnoback.nl', $this->buyerLeanpubInvoiceId)
        );
    }
}

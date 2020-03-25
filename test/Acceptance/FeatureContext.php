<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use LeanpubBookClub\Application\LeanpubSales;
use LeanpubBookClub\Application\RequestAccess;
use LeanpubBookClub\Domain\Model\Member\AccessGrantedToMember;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberId;
use PHPUnit\Framework\Assert;
use RuntimeException;

final class FeatureContext implements Context
{
    /**
     * @var ServiceContainerForAcceptanceTesting
     */
    private ServiceContainerForAcceptanceTesting $serviceContainer;

    private ?string $buyerLeanpubInvoiceId;

    private ?string $buyerEmailAddress;

    private ?MemberId $memberId;

    public function __construct()
    {
        $this->serviceContainer = new ServiceContainerForAcceptanceTesting();
    }

    /**
     * @Given someone has bought a copy of the book
     */
    public function someoneHasBoughtACopyOfTheBook(): void
    {
        $this->buyerEmailAddress = 'info@matthiasnoback.nl';
        $this->buyerLeanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';
    }

    /**
     * @When they request access to the club providing the correct invoice ID
     */
    public function theySignUpForTheClubProvidingTheCorrectInvoiceID()
    {
        Assert::assertNotNull($this->buyerLeanpubInvoiceId);
        Assert::assertNotNull($this->buyerEmailAddress);

        $this->invoiceIdIsOfAnActualPurchase(LeanpubInvoiceId::fromString($this->buyerLeanpubInvoiceId));

        $this->memberId = $this->serviceContainer->application()->requestAccess(
            new RequestAccess($this->buyerEmailAddress, $this->buyerLeanpubInvoiceId)
        );
    }

    /**
     * @Then they should be granted access to the club
     */
    public function theyShouldBeGrantedAccessToTheClub()
    {
        Assert::assertNotNull($this->memberId);

        foreach ($this->dispatchedEvents() as $event) {
            if ($event instanceof AccessGrantedToMember && $event->memberId()->equals($this->memberId)) {
                return;
            }
        }

        throw new RuntimeException('Expected an AccessGrantedToMember event for the member who requested access');
    }

    /**
     * @When someone requests access to the club providing an invoice ID that does not match an actual purchase
     */
    public function someoneRequestsAccessToTheClubProvidingAnInvoiceIDThatDoesNotMatchAnActualPurchase()
    {

    }

    /**
     * @Then they should not be granted access to the club
     */
    public function theyShouldNotBeGrantedAccessToTheClub()
    {
        foreach ($this->dispatchedEvents() as $event) {
            if ($event instanceof AccessGrantedToMember) {
                throw new RuntimeException('We did not expect an AccessGrantedToMember event to have been dispatched');
            }
        }
    }

    /**
     * @return array<object>
     */
    private function dispatchedEvents(): array
    {
        return $this->serviceContainer->eventDispatcher()->dispatchedEvents();
    }

    private function invoiceIdIsOfAnActualPurchase(LeanpubInvoiceId $leanpubInvoiceId): void
    {
        $this->serviceContainer->leanpubSales()->invoiceIdIsOfAnActualPurchase($leanpubInvoiceId);
    }
}

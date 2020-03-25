<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use LeanpubBookClub\Application\RequestAccess;
use LeanpubBookClub\Domain\Model\Member\AccessGrantedToMember;
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

        $events = $this->serviceContainer->eventDispatcher()->dispatchedEvents();

        foreach ($events as $event) {
            if ($event instanceof AccessGrantedToMember && $event->memberId()->equals($this->memberId)) {
                return;
            }
        }

        throw new RuntimeException('Expected an AccessGrantedToMember event for the member who requested access');
    }
}

<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseImported;
use LeanpubBookClub\Infrastructure\Leanpub\Purchase;
use PHPUnit\Framework\Assert;

final class ImportingContext extends FeatureContext
{
    /**
     * @Given Leanpub returns to us the following list of individual purchases:
     * @param TableNode<mixed> $table
     */
    public function leanpubReturnsToUsTheFollowingListOfIndividualPurchases(TableNode $table): void
    {
        foreach ($table->getHash() as $purchaseData) {
            $this->serviceContainer()->individualPurchases()->add(
                new Purchase($purchaseData['Invoice ID'], $purchaseData['Purchase date'])
            );
        }
    }

    /**
     * @When the system imports all purchases
     * @Given the system has imported all purchases
     * @When the system imports all purchases again
     */
    public function theImporterRuns(): void
    {
        $this->clearEvents();

        $this->application()->importAllPurchases();
    }

    /**
     * @Then the imported invoice IDs should be:
     * @param TableNode<mixed> $table
     */
    public function theImportedInvoiceIdsShouldBe(TableNode $table): void
    {
        $actualInvoiceIds = array_map(
            function (PurchaseImported $event): string {
                return $event->leanpubInvoiceId()->asString();
            },
            $this->purchaseImportedEvents()
        );

        $expectedInvoiceIds = array_column($table->getHash(), 'Invoice ID');
        Assert::assertEquals($expectedInvoiceIds, $actualInvoiceIds);
    }

    /**
     * @Then no purchases should have been imported
     */
    public function noPurchasesShouldHaveBeenImported(): void
    {
        Assert::assertEquals([], $this->purchaseImportedEvents());
    }

    /**
     * @return array<PurchaseImported>
     */
    private function purchaseImportedEvents(): array
    {
        return array_filter(
            $this->dispatchedEvents(),
            function (object $event): bool {
                return $event instanceof PurchaseImported;
            }
        );
    }
}

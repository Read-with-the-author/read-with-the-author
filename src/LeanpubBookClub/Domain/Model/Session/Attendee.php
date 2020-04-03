<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use Doctrine\DBAL\Schema\Schema;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Infrastructure\Mapping;
use TalisOrm\AggregateId;
use TalisOrm\ChildEntity;
use TalisOrm\ChildEntityBehavior;
use TalisOrm\Schema\SpecifiesSchema;

final class Attendee implements ChildEntity, SpecifiesSchema
{
    use ChildEntityBehavior;
    use Mapping;

    private SessionId $sessionId;

    private LeanpubInvoiceId $memberId;

    private function __construct()
    {
    }

    public static function create(SessionId $sessionId, LeanpubInvoiceId $memberId): self
    {
        $attendee = new self();

        $attendee->sessionId = $sessionId;
        $attendee->memberId = $memberId;

        return $attendee;
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->memberId;
    }

    /**
     * @param array<string,mixed> $state
     * @param array<string,mixed> $aggregateState
     */
    public static function fromState(array $state, array $aggregateState): self
    {
        $instance = new self();

        $instance->sessionId = SessionId::fromString(self::asString($state, 'sessionId'));
        $instance->memberId = LeanpubInvoiceId::fromString(self::asString($state, 'memberId'));

        return $instance;
    }

    /**
     * @return array<string,mixed>
     */
    public function state(): array
    {
        return [
            'sessionId' => $this->sessionId->asString(),
            'memberId' => $this->memberId->asString()
        ];
    }

    public static function tableName(): string
    {
        return 'attendees';
    }

    /**
     * @return array<string,mixed>
     */
    public function identifier(): array
    {
        return [
            'sessionId' => $this->sessionId->asString(),
            'memberId' => $this->memberId->asString()
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return [
            'sessionId' => (string)$aggregateId
        ];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('sessionId', 'string')->setNotnull(true);
        $table->addIndex(['sessionId']);

        $table->addColumn('memberId', 'string')->setNotnull(true);
    }
}

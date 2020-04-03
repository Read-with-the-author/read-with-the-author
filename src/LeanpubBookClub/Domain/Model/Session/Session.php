<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use Assert\Assert;
use Doctrine\DBAL\Schema\Schema;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Session implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    private SessionId $sessionId;

    private ScheduledDate $date;

    private string $description;

    private int $maximumNumberOfAttendees;

    /**
     * @var array<Attendee> & Attendee[]
     */
    private array $attendees = [];

    private bool $wasClosed = false;

    private ?string $urlForCall = null;

    private function __construct()
    {
    }

    public static function plan(
        SessionId $sessionId,
        ScheduledDate $date,
        string $description,
        int $maximumNumberOfAttendees
    ): self {
        $session = new self();

        Assert::that($description)->notEmpty('The session description should not be empty');
        Assert::that($maximumNumberOfAttendees)
            ->greaterThan(0, 'The maximum number of participants should be greater than 0');

        $session->sessionId = $sessionId;
        $session->date = $date;
        $session->description = $description;
        $session->maximumNumberOfAttendees = $maximumNumberOfAttendees;

        $session->events[] = new SessionWasPlanned($sessionId, $date, $description, $maximumNumberOfAttendees);

        return $session;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function attend(LeanpubInvoiceId $memberId): void
    {
        if ($this->wasClosed) {
            // When the session was closed, we don't accept new attendees
            return;
        }

        foreach ($this->attendees as $attendee) {
            if ($attendee->memberId()->equals($memberId)) {
                // No need to register the same attendee again
                return;
            }
        }

        $this->attendees[] = Attendee::create($this->sessionId, $memberId);

        $this->events[] = new AttendeeRegisteredForSession($this->sessionId, $memberId, $this->date);

        if (count($this->attendees) >= $this->maximumNumberOfAttendees) {
            $this->close();
        }
    }

    private function close(): void
    {
        $this->wasClosed = true;

        $this->events[] = new SessionWasClosedForRegistration($this->sessionId);
    }

    public function cancelAttendance(LeanpubInvoiceId $memberId): void
    {
        foreach ($this->attendees as $key => $attendee) {
            $this->deleteChildEntity($attendee);

            if ($attendee->memberId()->equals($memberId)) {
                unset($this->attendees[$key]);
                $this->events[] = new AttendeeCancelledTheirAttendance($this->sessionId, $memberId);
            }
        }
    }

    public function setCallUrl(string $urlForCall): void
    {
        if ($urlForCall === $this->urlForCall) {
            return;
        }

        $this->urlForCall = $urlForCall;

        $this->events[] = new UrlForCallWasUpdated($this->sessionId, $urlForCall);
    }

    public function update(string $description, string $urlForCall): void
    {
        $this->setDescription($description);

        $this->setCallUrl($urlForCall);
    }

    private function setDescription(string $description): void
    {
        if ($this->description === $description) {
            return;
        }

        $this->description = $description;

        $this->events[] = new DescriptionWasUpdated($this->sessionId, $description);
    }

    /**
     * @return array<int,class-string>
     */
    public static function childEntityTypes(): array
    {
        return [
            Attendee::class
        ];
    }

    /**
     * @return array<string,array<object>>
     */
    public function childEntitiesByType(): array
    {
        return [
            Attendee::class => $this->attendees
        ];
    }

    /**
     * @param array<string,mixed> $aggregateState
     * @param array<string,array<object>> $childEntitiesByType
     */
    public static function fromState(array $aggregateState, array $childEntitiesByType): self
    {
        $instance = new self();

        $instance->sessionId = SessionId::fromString(self::asString($aggregateState, 'sessionId'));
        $instance->date = ScheduledDate::fromString(self::asString($aggregateState, 'date'));
        $instance->description = self::asString($aggregateState, 'description');
        $instance->maximumNumberOfAttendees = self::asInt($aggregateState, 'maximumNumberOfAttendees');
        $instance->wasClosed = self::asBool($aggregateState, 'wasClosed');
        $instance->urlForCall = self::asStringOrNull($aggregateState, 'urlForCall');

        $attendees = $childEntitiesByType[Attendee::class];
        Assert::that($attendees)->all()->isInstanceOf(Attendee::class);
        $instance->attendees = $attendees;

        return $instance;
    }

    /**
     * @return array<string,mixed>
     */
    public function state(): array
    {
        return [
            'sessionId' => $this->sessionId->asString(),
            'date' => $this->date->asString(),
            'description' => $this->description,
            'maximumNumberOfAttendees' => $this->maximumNumberOfAttendees,
            'wasClosed' => $this->wasClosed,
            'urlForCall' => $this->urlForCall
        ];
    }

    public static function tableName(): string
    {
        return 'sessions';
    }

    /**
     * @return array<string,mixed>
     */
    public function identifier(): array
    {
        return [
            'sessionId' => $this->sessionId->asString()
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
        $table->setPrimaryKey(['sessionId']);

        $table->addColumn('date', 'string')->setNotnull(true);
        $table->addColumn('description', 'string')->setNotnull(true);
        $table->addColumn('maximumNumberOfAttendees', 'integer')->setNotnull(true);
        $table->addColumn('wasClosed', 'boolean')->setNotnull(true);
        $table->addColumn('urlForCall', 'string')->setNotnull(false);

        Attendee::specifySchema($schema);
    }
}

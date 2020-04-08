<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use DateTimeImmutable;
use Doctrine\DBAL\Schema\Schema;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;
use LeanpubBookClub\Infrastructure\Mapping;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Member implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;
    use Mapping;

    private LeanpubInvoiceId $memberId;

    private EmailAddress $emailAddress;

    private ?AccessToken $accessToken = null;

    private TimeZone $timeZone;

    private bool $wasGrantedAccess = false;

    private DateTimeImmutable $requestedAccessAt;

    private function __construct()
    {
    }

    public static function requestAccess(
        LeanpubInvoiceId $leanpubInvoiceId,
        EmailAddress $emailAddress,
        TimeZone $timeZone,
        DateTimeImmutable $requestedAccessAt
    ): self {
        $member = new self();

        $member->memberId = $leanpubInvoiceId;
        $member->emailAddress = $emailAddress;
        $member->timeZone = $timeZone;
        $member->requestedAccessAt = $requestedAccessAt;

        $member->events[] = new MemberRequestedAccess($leanpubInvoiceId, $emailAddress, $timeZone, $requestedAccessAt);

        return $member;
    }

    public function grantAccess(): void
    {
        if ($this->wasGrantedAccess) {
            return;
        }

        $this->wasGrantedAccess = true;

        $this->events[] = new AccessWasGrantedToMember($this->memberId, $this->emailAddress);
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->memberId;
    }

    public function generateAccessToken(AccessTokenGenerator $accessTokenGenerator): void
    {
        $this->accessToken = $accessTokenGenerator->generate();

        $this->events[] = new AnAccessTokenWasGenerated($this->memberId, $this->emailAddress, $this->accessToken);
    }

    public function changeTimeZone(TimeZone $newTimeZone): void
    {
        $this->timeZone = $newTimeZone;

        $this->events[] = new MemberTimeZoneChanged($this->memberId, $newTimeZone);
    }

    /**
     * @return array<int,class-string>
     */
    public static function childEntityTypes(): array
    {
        return [];
    }

    /**
     * @return array<string,array<object>>
     */
    public function childEntitiesByType(): array
    {
        return [];
    }

    /**
     * @param array<string,mixed> $aggregateState
     * @param array<string,array<object>> $childEntitiesByType
     */
    public static function fromState(array $aggregateState, array $childEntitiesByType): self
    {
        $instance = new self();

        $instance->memberId = LeanpubInvoiceId::fromString(self::asString($aggregateState, 'memberId'));
        $instance->emailAddress = EmailAddress::fromString(self::asString($aggregateState, 'emailAddress'));
        $instance->timeZone = TimeZone::fromString(self::asString($aggregateState, 'timeZone'));

        $accessToken = self::asStringOrNull($aggregateState, 'accessToken');
        $instance->accessToken = is_string($accessToken) ? AccessToken::fromString($accessToken) : null;

        $instance->wasGrantedAccess = self::asBool($aggregateState, 'wasGrantedAccess');
        $instance->requestedAccessAt = self::dateTimeAsDateTimeImmutable($aggregateState, 'requestedAccessAt');

        return $instance;
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function state(): array
    {
        return [
            'memberId' => $this->memberId->asString(),
            'emailAddress' => $this->emailAddress->asString(),
            'timeZone' => $this->timeZone->asString(),
            'accessToken' => $this->accessToken instanceof AccessToken ? $this->accessToken->asString() : null,
            'wasGrantedAccess' => $this->wasGrantedAccess,
            'requestedAccessAt' => self::dateTimeImmutableAsDateTimeString($this->requestedAccessAt)
        ];
    }

    public static function tableName(): string
    {
        return 'members';
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function identifier(): array
    {
        return [
            'memberId' => $this->memberId->asString()
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return [
            'memberId' => (string)$aggregateId
        ];
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('memberId', 'string')->setNotnull(true);
        $table->setPrimaryKey(['memberId']);

        $table->addColumn('emailAddress', 'string')->setNotnull(true);
        $table->addColumn('accessToken', 'string')->setNotnull(false);
        $table->addColumn('timeZone', 'string')->setNotnull(true);
        $table->addColumn('wasGrantedAccess', 'boolean')->setNotnull(true)->setDefault(false);
        $table->addColumn('requestedAccessAt', 'datetime')->setNotnull(false);
    }
}

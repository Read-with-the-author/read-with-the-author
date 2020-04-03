<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use Doctrine\DBAL\Schema\Schema;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Member implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;

    private LeanpubInvoiceId $memberId;

    private EmailAddress $emailAddress;

    private ?AccessToken $accessToken = null;

    private TimeZone $timeZone;

    private function __construct()
    {
    }

    public static function requestAccess(
        LeanpubInvoiceId $leanpubInvoiceId,
        EmailAddress $emailAddress,
        TimeZone $timeZone
    ): self {
        $member = new self();

        $member->memberId = $leanpubInvoiceId;
        $member->emailAddress = $emailAddress;
        $member->timeZone = $timeZone;

        $member->events[] = new MemberRequestedAccess($leanpubInvoiceId, $emailAddress, $timeZone);

        return $member;
    }

    public function grantAccess(): void
    {
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

    public function childEntitiesByType(): array
    {
        return [];
    }

    public static function childEntityTypes(): array
    {
        return [];
    }

    public static function fromState(array $aggregateState, array $childEntitiesByType): self
    {
        $instance = new self();

        $instance->memberId = LeanpubInvoiceId::fromString($aggregateState['memberId']);
        $instance->emailAddress = EmailAddress::fromString($aggregateState['emailAddress']);
        $instance->timeZone = TimeZone::fromString($aggregateState['timeZone']);
        $instance->accessToken = is_string($aggregateState['accessToken'])
            ? AccessToken::fromString($aggregateState['accessToken'])
            : null;

        return $instance;
    }

    public function state(): array
    {
        return [
            'memberId' => $this->memberId->asString(),
            'emailAddress' => $this->emailAddress->asString(),
            'timeZone' => $this->timeZone->asString(),
            'accessToken' => $this->accessToken instanceof AccessToken ? $this->accessToken->asString() : null
        ];
    }

    public static function tableName(): string
    {
        return 'members';
    }

    public function identifier(): array
    {
        return [
            'memberId' => $this->memberId->asString()
        ];
    }

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
    }
}

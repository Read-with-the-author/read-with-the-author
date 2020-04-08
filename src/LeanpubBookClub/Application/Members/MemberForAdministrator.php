<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Members;

final class MemberForAdministrator
{
    private string $memberId;

    private string $emailAddress;

    private string $requestedAccessAt;

    private bool $accessWasGranted;

    public function __construct(
        string $memberId,
        string $emailAddress,
        string $requestedAccessAt,
        bool $accessWasGranted
    ) {
        $this->memberId = $memberId;
        $this->emailAddress = $emailAddress;
        $this->requestedAccessAt = $requestedAccessAt;
        $this->accessWasGranted = $accessWasGranted;
    }

    public function memberId(): string
    {
        return $this->memberId;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function requestedAccessAt(): string
    {
        return $this->requestedAccessAt;
    }

    public function accessWasGranted(): bool
    {
        return $this->accessWasGranted;
    }
}

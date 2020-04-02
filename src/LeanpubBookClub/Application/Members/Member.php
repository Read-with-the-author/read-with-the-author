<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Members;

use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use Symfony\Component\Security\Core\User\UserInterface;

final class Member implements UserInterface
{
    private string $memberId;

    private string $timeZone;

    private string $emailAddress;

    public function __construct(string $memberId, string $timeZone, string $emailAddress)
    {
        $this->memberId = $memberId;
        $this->timeZone = $timeZone;
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return ['ROLE_MEMBER'];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->memberId;
    }

    public function timeZone(): string
    {
        return $this->timeZone;
    }

    public function memberId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString($this->memberId);
    }

    public function emailAddress(): EmailAddress
    {
        return EmailAddress::fromString($this->emailAddress);
    }

    public function eraseCredentials(): void
    {
    }
}

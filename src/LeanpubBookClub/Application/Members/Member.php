<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Members;

use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use Symfony\Component\Security\Core\User\UserInterface;

final class Member implements UserInterface
{
    private string $memberId;

    private string $timeZone;

    public function __construct(string $memberId, string $timeZone)
    {
        $this->memberId = $memberId;
        $this->timeZone = $timeZone;
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

    public function eraseCredentials(): void
    {
    }
}

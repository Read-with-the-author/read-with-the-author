<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Security;

use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class MemberUserProvider implements UserProviderInterface
{
    private Members $members;

    public function __construct(Members $members)
    {
        $this->members = $members;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        try {
            return $this->members->getOneById($username);
        } catch (CouldNotFindMember $exception) {
            throw new UsernameNotFoundException('User not found', 0, $exception);
        }
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Member) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return $class === Member::class;
    }
}

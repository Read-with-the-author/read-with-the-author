<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberId;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;

final class Application
{
    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher
    ) {
        $this->memberRepository = $memberRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function requestAccess(RequestAccess $command): MemberId
    {
        $memberId = $this->memberRepository->nextIdentity();

        $member = Member::requestAccess($memberId, $command->emailAddress(), $command->leanpubInvoiceId());

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());

        return $member->memberId();
    }

    public function grantAccess(MemberId $memberId): void
    {
        $member = $this->memberRepository->getById($memberId);

        $member->grantAccess();

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }
}

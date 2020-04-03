<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use Assert\Assert;
use LeanpubBookClub\Domain\Model\Session\CouldNotFindSession;
use LeanpubBookClub\Domain\Model\Session\Session;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use Ramsey\Uuid\Uuid;
use TalisOrm\AggregateNotFoundException;
use TalisOrm\AggregateRepository;

final class SessionTalisOrmRepository implements SessionRepository
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function save(Session $session): void
    {
        $this->aggregateRepository->save($session);
    }

    public function getById(SessionId $sessionId): Session
    {
        try {
            $session = $this->aggregateRepository->getById(
                Session::class,
                $sessionId
            );
            Assert::that($session)->isInstanceOf(Session::class);
            /** @var Session $session */

            return $session;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindSession::withId($sessionId);
        }
    }

    public function nextIdentity(): SessionId
    {
        return SessionId::fromString(Uuid::uuid4()->toString());
    }
}

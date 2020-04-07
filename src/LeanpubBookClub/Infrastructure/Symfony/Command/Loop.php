<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Command;

use LeanpubBookClub\Application\EventDispatcher;
use Throwable;

final class Loop
{
    private int $delay;

    private EventDispatcher $eventDispatcher;

    /**
     * @var callable
     */
    private $loopFunction;

    private bool $weShouldStop = false;

    public function __construct(
        int $delay,
        callable $loopFunction,
        EventDispatcher $eventDispatcher
    ) {
        $this->delay = $delay;
        $this->loopFunction = $loopFunction;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function run(bool $keepRunning = true): void
    {
        $this->initialize();

        while (true) {
            try {
                ($this->loopFunction)();
            } catch (Throwable $throwable) {
                // @todo dispatch loggable event
            } finally {
                if (!$keepRunning || $this->weShouldStop) {
                    /*
                     * When we receive a terminating signal from the manager of this process we can simply return and
                     * wait for the command line process to naturally reach its exit statement, allowing anything in
                     * between to terminate or clean things up correctly as well.
                     */
                    return;
                }
            }

            sleep($this->delay);
        }
    }

    private function initialize(): void
    {
        pcntl_async_signals(true);

        pcntl_signal(
            SIGINT,
            function () {
                echo "Received SIGINT, waiting...\n";

                $this->stop();
            }
        );
        pcntl_signal(
            SIGTERM,
            function () {
                echo "Received SIGTERM, waiting...\n";

                $this->stop();
            }
        );
    }

    public function stop(): void
    {
        $this->weShouldStop = true;
    }
}

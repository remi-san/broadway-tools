<?php


namespace Broadway\Tools\Command;


use Broadway\CommandHandling\CommandBus;
use Broadway\CommandHandling\CommandHandler;
use Exception;
use Psr\Log\LoggerInterface;
use RemiSan\TransactionManager\TransactionManager;
use Throwable;

class TransactionalCommandBus
{
    /** @var CommandBus */
    private $decorated;
    /** @var TransactionManager */
    private $transactionManager;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(CommandBus $bus, TransactionManager $transactionManager, LoggerInterface $logger)
    {
        $this->decorated = $bus;
        $this->transactionManager = $transactionManager;
        $this->logger = $logger;
    }

    /**
     * @param mixed $command
     *
     * @throws Throwable
     */
    public function dispatch($command)
    {
        $this->transactionManager->beginTransaction();

        try {
            $this->decorated->dispatch($command);
            $this->transactionManager->commit();
        } catch (Exception $exception) {
            $this->rollback($exception);
            throw $exception;
        }
    }

    /**
     * @param CommandHandler $handler
     */
    public function subscribe(CommandHandler $handler)
    {
        $this->decorated->subscribe($handler);
    }

    /**
     * @param $exception
     *
     * @throws Exception
     */
    private function rollback($exception)
    {
        try {
            $this->transactionManager->rollback();
        } catch (Exception $rollbackException) {
            $this->logger->critical($exception);
            throw $rollbackException;
        }
    }
}
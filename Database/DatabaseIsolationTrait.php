<?php

namespace Akuma\Component\Testing\Database;

use Akuma\Component\Testing\Helper\TestHelper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Client;

trait DatabaseIsolationTrait
{
    /** @var array */
    private static $dbIsolationPerTest = [];

    /** @var array */
    private static $nestTransactionsWithSavePoints = [];

    /**
     * @var Connection[]
     */
    protected static $isolatedConnections = [];

    /**
     * @return Client
     */
    abstract protected function getClient();

    /**
     * @param bool $nestTransactionsWithSavePoints
     */
    protected function startTransaction($nestTransactionsWithSavePoints = false)
    {
        if (false == $this->getClient() instanceof Client) {
            throw new \LogicException('The client must be instance of Client');
        }
        if (false == $this->getClient()->getContainer()) {
            throw new \LogicException('The client missing a container. Make sure the kernel was booted');
        }

        /** @var RegistryInterface $registry */
        $registry = $this->getClient()->getContainer()->get('doctrine');
        foreach ($registry->getManagers() as $name => $em) {
            if ($em instanceof EntityManagerInterface) {
                $em->clear();
                $connection = $em->getConnection();
                if ($connection->getNestTransactionsWithSavepoints() !== $nestTransactionsWithSavePoints) {
                    $connection->setNestTransactionsWithSavepoints($nestTransactionsWithSavePoints);
                }
                $connection->beginTransaction();

                self::$isolatedConnections[$name . uniqid('connection', true)] = $connection;
            }
        }
    }

    protected static function rollbackTransaction()
    {
        foreach (self::$isolatedConnections as $connection) {
            while ($connection->isConnected() && $connection->isTransactionActive()) {
                $connection->rollBack();
            }
        }

        self::$isolatedConnections = [];
    }

    /**
     * Get value of dbIsolationPerTest option from annotation of called class
     *
     * @return bool
     */
    private static function getDbIsolationPerTestSetting()
    {
        $calledClass = get_called_class();
        if (!isset(self::$dbIsolationPerTest[$calledClass])) {
            self::$dbIsolationPerTest[$calledClass] = TestHelper::isAnnotationExists(
                $calledClass,
                DatabaseIsolationInterface::DB_ISOLATION_PER_TEST_ANNOTATION
            );
        }

        return self::$dbIsolationPerTest[$calledClass];
    }

    /**
     * @return bool
     */
    private static function hasNestTransactionsWithSavePoints()
    {
        $calledClass = get_called_class();
        if (!isset(self::$nestTransactionsWithSavePoints[$calledClass])) {
            self::$nestTransactionsWithSavePoints[$calledClass] =
                TestHelper::isAnnotationExists(
                    $calledClass,
                    DatabaseIsolationInterface::NEST_TRANSACTIONS_WITH_SAVE_POINTS
                );
        }

        return self::$nestTransactionsWithSavePoints[$calledClass];
    }
}

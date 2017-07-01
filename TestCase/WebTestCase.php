<?php

namespace Akuma\Component\Testing\TestCase;

use Akuma\Component\Testing\Database\DatabaseIsolationTrait;
use Akuma\Component\Testing\Database\DataFixtureTrait;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class WebTestCase extends BaseWebTestCase
{
    use DatabaseIsolationTrait;
    use DataFixtureTrait;

    /** @var Client */
    private static $clientInstance;

    /** @var Client */
    protected $client;

    /**
     * @after
     */
    protected function afterTest()
    {
        if (self::getDbIsolationPerTestSetting()) {
            $this->rollbackTransaction();
            self::resetClient();
        }
    }

    /**
     * @beforeClass
     */
    public static function beforeClass()
    {
        self::resetClient();
    }

    /**
     * @afterClass
     */
    public static function afterClass()
    {
        self::rollbackTransaction();
    }

    /**
     * Reset client and rollback transaction
     */
    protected static function resetClient()
    {
        static::ensureKernelShutdown();
    }

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server An array of server parameters
     *
     * @return Client
     */
    protected function initClient(array $options = [], array $server = [])
    {
        if (!self::$clientInstance) {
            self::$clientInstance = static::createClient($options, $server);

            $this->startTransaction(self::hasNestTransactionsWithSavePoints());
        } else {
            self::$clientInstance->setServerParameters($server);
        }

        return $this->client = self::$clientInstance;
    }

    /**
     * {@inheritdoc}
     *
     * @return Client
     */
    protected function getClient()
    {
        return self::getClientInstance();
    }

    /**
     * Get an instance of the dependency injection container.
     *
     * @return ContainerInterface
     */
    protected static function getContainer()
    {
        return static::getClientInstance()->getContainer();
    }

    /**
     * @return Client
     * @throws \BadMethodCallException
     */
    public static function getClientInstance()
    {
        if (!self::$clientInstance) {
            throw new \BadMethodCallException('Client instance is not initialized.');
        }

        return self::$clientInstance;
    }
}

<?php

namespace Akuma\Component\Testing\Database;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

trait DataFixtureTrait
{
    /**
     * @return Client
     */
    abstract protected function getClient();

    /**
     * @param string[] $fixtures
     *
     * @link https://github.com/nelmio/alice
     */
    protected function loadFixtures(array $fixtures)
    {
        $container = $this->getClient()->getContainer();
        $em = $container->get('doctrine')->getManager();

        $ormLoader = new Loader();
        foreach ($fixtures as $k => $fixture) {
            if (class_exists($fixture)) {
                /** @var FixtureInterface $fixture */
                $fixture = new $fixture;
                if ($fixture instanceof ContainerAwareInterface) {
                    $fixture->setContainer($container);
                }
                $ormLoader->addFixture($fixture);
                unset($fixtures[$k]);
            }
        }

        if ($ormLoader->getFixtures()) {
            $executor = new ORMExecutor($em);
            $executor->execute($ormLoader->getFixtures(), true);
        }

        if (count($fixtures)) {
            \Nelmio\Alice\Fixtures::load($fixtures, $em);
        }
    }
}

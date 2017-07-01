# Akuma Testing Component

Inspired by [OroTestFrameworkBundle](https://github.com/orocrm/platform/tree/master/src/Oro/Bundle/TestFrameworkBundle) 

### Installation

```
    composer require akuma/testing-component
```

### Usage

Modify for your needs and place to the root og your project [phpunit.xml.dist](./Resources/dist/phpunit.xml.dist)

##### Test Case
```
<?php

namespace Acme\Bundle\SampleBundle\Tests\Functional;

use Akuma\Component\Testing\TestCase\WebTestCase;
use Acme\Bundle\SampleBundle\Entity\SomeEntity;

class AcmeSampleTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initClient(); // Required
        $this->loadFixtures([
            \Acme\Bundle\SampleBundle\Tests\Functional\Fixtures\TestFixture::class,
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Fixtures/alice_fixture.yml',
        ]);
    }

    public function testXXX()
    {
        var_dump($this->getContainer()->get('doctrine')->getRepository(SomeEntity::class)->findAll([]));
    }
}
```

##### Test Doctrine Fixture

```
<?php

namespace \Acme\Bundle\SampleBundle\Tests\Functional\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\Bundle\SampleBundle\Entity\SomeEntity;

class TestFixture implements FixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $x = new SomeEntity();
        $x->setDomain('xxx.com')->setToken('xxx');
        $manager->persist($x);
        $manager->flush();
    }
}
```

##### Test Alice Fixture

```
Acme\Bundle\SampleBundle\Entity\SomeEntity:
    test0:
        domain: 'zzz.com'
        token: 'zzz'
```

##### SomeEntity
```
<?php

namespace Acme\Bundle\SampleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="some_entity_table")
 */
class SomeEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=false, unique=true)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=false, unique=true)
     */
    private $token;

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
```
##### Then execute functional tests

```
phpunit --testsuite=functional
```

Sample Output:
```
PHPUnit 5.7.21 by Sebastian Bergmann and contributors.

Testing
.                                                                   1 / 1 (100%)
array(2) {
  [0] =>
  class Acme\Bundle\SampleBundle\Entity\SomeEntity#??? (3) {
    private $domain =>
    string(7) "xxx.com"
    private $token =>
    string(3) "xxx"
    private $id =>
    int(14)
  }
  [1] =>
  Acme\Bundle\SampleBundle\Entity\SomeEntity#??? (3) {
    private $domain =>
    string(7) "zzz.com"
    private $token =>
    string(3) "zzz"
    private $id =>
    int(15)
  }
}
```

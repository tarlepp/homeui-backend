<?php
declare(strict_types=1);
/**
 * /src/App/Tests/ContainerTestCase.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Tests;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContainerTestCase
 *
 * @package App\Tests
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
abstract class ContainerTestCase extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Getter method for container
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if (!($this->container instanceof ContainerInterface)) {
            self::bootKernel();

            $this->container = static::$kernel->getContainer();
        }

        return $this->container;
    }
}

<?php
declare(strict_types = 1);
/**
 * /spec/App/Repository/SensorSpec.php
 *
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
namespace spec\App\Repository;

use App\Entity\Sensor as SensorEntity;
use App\Entity\Interfaces\EntityInterface;
use App\Repository\Sensor;
use App\Repository\Interfaces\Base as RepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

/**
 * Class SensorSpec
 *
 * @package spec\App\Repository
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
class SensorSpec extends ObjectBehavior
{
    /**
     * @param   \PhpSpec\Wrapper\Collaborator|EntityManager $entityManager
     */
    function let(
        EntityManager $entityManager
    ) {
        // Get entity class meta data
        $classMetaData = new ClassMetadata(SensorEntity::class);

        // Mock entity manager to return created class meta data object
        $entityManager->getClassMetadata(SensorEntity::class)->willReturn($classMetaData);

        // And assign specified constructor parameters
        $this->beConstructedWith($entityManager, $classMetaData);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Sensor::class);
        $this->shouldImplement(RepositoryInterface::class);
    }

    function it_should_return_expected_value_when_calling_getEntityName_method()
    {
        $this->getEntityName()->shouldBe(SensorEntity::class);
    }

    function it_should_return_expected_value_when_calling_getAssociations_method()
    {
        $this->getAssociations()->shouldBeArray();
    }

    function it_should_return_expected_value_when_calling_getSearchColumns_method()
    {
        $expected = ['name', 'description', 'ip', 'snmp_oid'];

        $this->getSearchColumns()->shouldBeArray();
        $this->getSearchColumns()->shouldReturn($expected);
    }

    function it_should_return_expected_value_when_calling_getEntityManager_method()
    {
        $this->getEntityManager()->shouldReturnAnInstanceOf(EntityManager::class);
    }

    /**
     * @param   \PhpSpec\Wrapper\Collaborator|EntityManager     $entityManager
     * @param   \PhpSpec\Wrapper\Collaborator|EntityInterface   $entity
     */
    function it_should_persist_and_flush_on_save_method(
        EntityManager $entityManager,
        EntityInterface $entity
    ) {
        $entityManager->persist($entity)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->save($entity);
    }

    /**
     * @param   \PhpSpec\Wrapper\Collaborator|EntityManager     $entityManager
     * @param   \PhpSpec\Wrapper\Collaborator|EntityInterface   $entity
     */
    function it_should_remove_and_flush_on_remove_method(
        EntityManager $entityManager,
        EntityInterface $entity
    ) {
        $entityManager->remove($entity)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->remove($entity);
    }
}

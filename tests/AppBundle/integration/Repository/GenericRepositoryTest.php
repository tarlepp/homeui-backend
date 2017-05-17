<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/integration/EventListener/GenericRepositoryTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\integration\Repository;

use App\Entity\User as UserEntity;
use App\Repository\User as UserRepository;
use App\Tests\Helpers\PHPUnitUtil;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Composite as CompositeExpression;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ExceptionListenerTest
 *
 * @package AppBundle\integration\Repository
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class GenericRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var UserEntity
     */
    protected $entity;

    /**
     * @var string
     */
    protected $entityName = UserEntity::class;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        self::bootKernel();

        // Store container and entity manager
        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var UserRepository repository */
        $this->repository = $this->entityManager->getRepository($this->entityName);
    }

    public function testThatGetExpressionDoesNotModifyExpressionWithEmptyCriteria()
    {
        $queryBuilder = $this->repository->createQueryBuilder('entity');
        $expression = $queryBuilder->expr()->andX();

        $output = PHPUnitUtil::callMethod($this->repository, 'getExpression', [$queryBuilder, $expression, []]);

        $message = 'getExpression method did modify expression with no criteria - this should not happen';

        static::assertSame($expression, $output, $message);
    }

    /**
     * @dataProvider dataProviderTestThatGetExpressionCreatesExpectedDqlAndParametersWithSimpleCriteria
     *
     * @param   array   $criteria
     * @param   string  $expectedDQL
     * @param   array   $expectedParameters
     */
    public function testThatGetExpressionCreatesExpectedDqlAndParametersWithSimpleCriteria(
        array $criteria,
        string $expectedDQL,
        array $expectedParameters
    ) {
        $queryBuilder = $this->repository->createQueryBuilder('u');
        $expression = $queryBuilder->expr()->andX();

        /** @var CompositeExpression $output */
        $queryBuilder->andWhere(
            PHPUnitUtil::callMethod($this->repository, 'getExpression', [$queryBuilder, $expression, [$criteria]])
        );

        static::assertSame($expectedDQL, $queryBuilder->getQuery()->getDQL());

        /** @var \Doctrine\Orm\Query\Parameter $parameter */
        foreach ($queryBuilder->getParameters()->toArray() as $key => $parameter) {
            static::assertSame($expectedParameters[$key]['name'], $parameter->getName());
            static::assertSame($expectedParameters[$key]['value'], $parameter->getValue());
        }
    }

    /**
     * @dataProvider dataProviderTestThatGetExpressionCreatesExpectedDqlAndParametersWithComplexCriteria
     *
     * @param   array   $criteria
     * @param   string  $expectedDQL
     * @param   array   $expectedParameters
     */
    public function testThatGetExpressionCreatesExpectedDqlAndParametersWithComplexCriteria(
        array $criteria,
        string $expectedDQL,
        array $expectedParameters
    ) {
        $queryBuilder = $this->repository->createQueryBuilder('u');
        $expression = $queryBuilder->expr()->andX();

        /** @var CompositeExpression $output */
        $queryBuilder->andWhere(
            PHPUnitUtil::callMethod($this->repository, 'getExpression', [$queryBuilder, $expression, $criteria])
        );

        static::assertSame($expectedDQL, $queryBuilder->getQuery()->getDQL());

        /** @var \Doctrine\Orm\Query\Parameter $parameter */
        foreach ($queryBuilder->getParameters()->toArray() as $key => $parameter) {
            static::assertSame($expectedParameters[$key]['name'], $parameter->getName());
            static::assertSame($expectedParameters[$key]['value'], $parameter->getValue());
        }
    }

    /**
     * @return array
     */
    public function dataProviderTestThatGetExpressionCreatesExpectedDqlAndParametersWithSimpleCriteria(): array
    {
        return [
            [
                ['u.id', 'eq', 123],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id = ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 123,
                    ],
                ],
            ],
            [
                ['u.id', 'neq', 123],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id <> ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 123,
                    ],
                ],
            ],
            [
                ['u.id', 'lt', 123],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id < ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 123,
                    ],
                ],
            ],
            [
                ['u.id', 'lte', 123],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id <= ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 123,
                    ],
                ],
            ],
            [
                ['u.id', 'gt', 123],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id > ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 123,
                    ],
                ],
            ],
            [
                ['u.id', 'gte', 123],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id >= ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 123,
                    ],
                ],
            ],
            [
                ['u.id', 'in', [1,2]],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id IN(1, 2)',
                [
                    [
                        'name'  => '1',
                        'value' => 123,
                    ],
                ],
            ],
            [
                ['u.id', 'notIn', [1,2]],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id NOT IN(1, 2)',
                [
                    [
                        'name'  => '1',
                        'value' => 1,
                    ],
                    [
                        'name'  => '2',
                        'value' => 2,
                    ],
                ],
            ],
            [
                ['u.id', 'isNull', null],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id IS NULL',
                [],
            ],
            [
                ['u.id', 'isNotNull', null],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id IS NOT NULL',
                [],
            ],
            [
                ['u.id', 'like', 'abc'],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id LIKE ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 'abc',
                    ],
                ],
            ],
            [
                ['u.id', 'notLike', 'abc'],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id NOT LIKE ?1',
                [
                    [
                        'name'  => '1',
                        'value' => 'abc',
                    ],
                ],
            ],
            [
                ['u.id', 'between', [1,6]],
                /** @lang text */
                'SELECT u FROM App\Entity\User u WHERE u.id BETWEEN ?1 AND ?2',
                [
                    [
                        'name'  => '1',
                        'value' => 1,
                    ],
                    [
                        'name'  => '2',
                        'value' => 6,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataProviderTestThatGetExpressionCreatesExpectedDqlAndParametersWithComplexCriteria(): array
    {
        return [
            [
                [
                    'and' => [
                        ['u.firstname', 'eq',   'foo bar'],
                        ['u.surname',   'neq',  'bar'],
                    ],
                    'or' => [
                        ['u.firstname', 'eq',   'bar foo'],
                        ['u.surname',   'neq',  'foo'],
                    ],
                ],
                /** @lang text */
                <<<'DQL'
SELECT u FROM App\Entity\User u WHERE (u.firstname = ?1 AND u.surname <> ?2) AND (u.firstname = ?3 OR u.surname <> ?4)
DQL
,
                [
                    [
                        'name'  => '1',
                        'value' => 'foo bar',
                    ],
                    [
                        'name'  => '2',
                        'value' => 'bar',
                    ],
                    [
                        'name'  => '3',
                        'value' => 'bar foo',
                    ],
                    [
                        'name'  => '4',
                        'value' => 'foo',
                    ],
                ],
            ],
            [
                [
                    'or' => [
                        ['u.field1', 'like', '%field1Value%'],
                        ['u.field2', 'like', '%field2Value%'],
                    ],
                    'and' => [
                        ['u.field3', 'eq', 3],
                        ['u.field4', 'eq', 'four'],
                    ],
                    ['u.field5', 'neq', 5],

                ],
                /** @lang text */
                <<<'DQL'
SELECT u FROM App\Entity\User u WHERE (u.field1 LIKE ?1 OR u.field2 LIKE ?2) AND (u.field3 = ?3 AND u.field4 = ?4) AND u.field5 <> ?5
DQL
,
                [
                    [
                        'name'  => '1',
                        'value' => '%field1Value%',
                    ],
                    [
                        'name'  => '2',
                        'value' => '%field2Value%',
                    ],
                    [
                        'name'  => '3',
                        'value' => 3,
                    ],
                    [
                        'name'  => '4',
                        'value' => 'four',
                    ],
                    [
                        'name'  => '5',
                        'value' => 5,
                    ],
                ],
            ]
        ];
    }
}

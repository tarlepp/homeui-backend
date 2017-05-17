<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/functional/Repository/TranslationTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\functional\Repository;

use App\Entity\Interfaces\EntityInterface;
use App\Entity\Translation as Entity;
use App\Repository\Translation as Repository;
use App\Tests\RepositoryTestCase;

/**
 * Class AuthorTest
 *
 * @package AppBundle\Entity
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class TranslationTest extends RepositoryTestCase
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $entityName = Entity::class;

    /**
     * @var array
     */
    protected $associations = [
        'transUnit',
        'locale',
    ];

    /**
     * @var array
     */
    protected $entityProperties = [];
}

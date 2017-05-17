<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jukka
 * Date: 30/04/2017
 * Time: 14.42
 *
 * /src/App/Services/Rest/Sensor.php
 *
 * @author Jukka Tainio <jukka@tainio.fi>
 */

namespace App\Services\Rest;

use App\Entity\Sensor as Entity;
use App\Repository\Sensor as Repository;
use Doctrine\Common\Persistence\Proxy;

/**
 * Class Sensor
 *
 * @package App\Services\Rest
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 *
 * @method  Repository      getRepository(): Repository
 * @method  Proxy|Entity    getReference(string $id): Proxy
 * @method  Entity[]        find(array $criteria = null, array $orderBy = null, int $limit = null, int $offset = null, array $search = null): array
 * @method  null|Entity     findOne(string $id, bool $throwExceptionIfNotFound = null)
 * @method  null|Entity     findOneBy(array $criteria, array $orderBy = null, bool $throwExceptionIfNotFound = null)
 * @method  Entity          create(\stdClass $data): Entity
 * @method  Entity          save(Entity $entity, bool $skipValidation = null): Entity
 * @method  Entity          update(string $id, \stdClass $data): Entity
 * @method  Entity          delete(string $id): Entity
 */
class Sensor extends Base
{
    // Implement custom service methods here (copy paste copy paste)
}
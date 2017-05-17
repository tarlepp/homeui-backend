<?php
declare(strict_types=1);
/**
 * /src/App/Repository/UserLogin.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Repository;

/**
 * Doctrine repository class for UserLogin entities.
 *
 * @package App\Repository
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class UserLogin extends Base
{
    /**
     * Names of search columns.
     *
     * @var string[]
     */
    protected static $searchColumns = [];

    // Implement custom entity query methods here
}

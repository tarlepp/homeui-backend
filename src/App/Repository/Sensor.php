<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jukka
 * Date: 16/05/2017
 * Time: 12.53
 */

namespace App\Repository;


class Sensor extends Base
{
    /**
     * Names of search columns.
     *
     * @var string[]
     */
    protected static $searchColumns = ['name', 'description','ip'];

    // Implement custom entity query methods here
}
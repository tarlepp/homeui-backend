<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/integration/Traits/Rest/Methods/Create.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\integration\Traits\Rest\Methods;

use App\Controller\Interfaces\RestController;
use App\Traits\Rest\Methods\Find as FindTrait;

/**
 * Class Create - just a dummy class so that we can actually test that trait.
 *
 * @package AppBundle\integration\Traits\Rest\Methods
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
abstract class Find implements RestController
{
    use FindTrait;
}

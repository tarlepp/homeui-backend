<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: jukka
 * Date: 30/04/2017
 * Time: 13.57
 */

namespace App\Controller;

use App\Traits\Rest\Roles as RestAction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AuthorController
 *
 * @Route(
 *      service="app.controller.sensordata",
 *      path="/sensordata",
 *  )
 *
 *
 * @package App\Controller
 * @author  Jukka Tainio <jukka@tainio.fi>
 */

class SensorDataController extends RestController
{
    use RestAction\Anon\Find;
    use RestAction\Anon\FindOne;
    use RestAction\Anon\Count;
    use RestAction\Anon\Ids;
    use RestAction\User\Create;
    use RestAction\User\Update;
    use RestAction\Admin\Delete;
}
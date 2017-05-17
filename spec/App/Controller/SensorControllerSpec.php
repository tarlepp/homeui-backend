<?php
/**
 * Created by PhpStorm.
 * User: jukka
 * Date: 16/05/2017
 * Time: 13.10
 */
declare(strict_types = 1);

namespace spec\App\Controller;

use App\Controller\SensorController;
use App\Controller\Interfaces\RestController;
use App\Services\Rest\Helper\Interfaces\Response as RestHelperResponseInterface;
use App\Services\Rest\Interfaces\Base as ResourceServiceInterface;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;

/**
 * Class SensorControllerSpec
 *
 * @mixin SensorController
 *
 * @package spec\App\Controller
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
class SensorControllerSpec extends ObjectBehavior
{
    /**
     * @param   Collaborator|ResourceServiceInterface    $resourceService
     * @param   Collaborator|RestHelperResponseInterface $restHelperResponse
     */
    public function let(
        ResourceServiceInterface $resourceService,
        RestHelperResponseInterface $restHelperResponse
    ) {
        $restHelperResponse->setResourceService($resourceService);

        $this->beConstructedWith(
            $resourceService,
            $restHelperResponse
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SensorController::class);
        $this->shouldImplement(RestController::class);
    }
}

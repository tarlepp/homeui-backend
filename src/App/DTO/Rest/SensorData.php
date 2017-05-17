<?php
declare(strict_types = 1);
/**
 * /src/App/DTO/Rest/SensorData.php
 *
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
namespace App\DTO\Rest;

use App\Entity\SensorData as SensorDataEntity;
use App\Entity\Interfaces\EntityInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SensorData
 *
 * @package App\DTO\Rest
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
class SensorData implements Interfaces\RestDto
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 2, max = 255)
     *
     * @JMS\Type("string")
     */
    public $sensorid = '';

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @JMS\Type("float")
     */
    public $value;

    /**
     * @var datetime
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @JMS\Type("datetime")
     */
    public $stamp;

    /**
     * Method to load DTO data from sensordata entity.
     *
     * @param   EntityInterface|SensorDataEntity  $entity
     *
     * @return  Interfaces\RestDto|SensorData
     */
    public function load(EntityInterface $entity): Interfaces\RestDto
    {
        $this->id = $entity->getId();
        $this->sensorid = $entity->getSensorId();
        $this->value = $entity->getValue();
        $this->stamp = $entity->getStamp();
        return $this;
    }

    /**
     * Method to update specified entity with DTO data.
     *
     * @param   EntityInterface|SensorEntity    $entity
     *
     * @return  EntityInterface|SensorEntity
     */
    public function update(EntityInterface $entity): EntityInterface
    {
        $entity->setValue($this->value);
        $entity->setSamp($this->stamp);

        return $entity;
    }
}

<?php
declare(strict_types = 1);
/**
 * /src/App/DTO/Rest/Sensor.php
 *
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
namespace App\DTO\Rest;

use App\Entity\Sensor as SensorEntity;
use App\Entity\Interfaces\EntityInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Sensor
 *
 * @package App\DTO\Rest
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
class Sensor implements Interfaces\RestDto
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
    public $name = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @JMS\Type("string")
     */
    public $description = '';

    /**
     * Method to load DTO data from sensor entity.
     *
     * @param   EntityInterface|SensorEntity  $entity
     *
     * @return  Interfaces\RestDto|Sensor
     */
    public function load(EntityInterface $entity): Interfaces\RestDto
    {
        $this->id = $entity->getId();
        $this->name = $entity->getName();
        $this->description = $entity->getDescription();

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
        $entity->setName($this->name);
        $entity->setDescription($this->description);

        return $entity;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jukka
 * Date: 03/05/2017
 * Time: 19.41
 *
 * @author Jukka Tainio <jukka@tainio.fi>
 *
 */

namespace App\Entity;

use App\Doctrine\Behaviours as ORMBehaviors;
use App\Entity\Interfaces\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * SensorType
 *
 * @ORM\Table(
 *      name="sensortype",
 *      indexes={
 *          @ORM\Index(name="created_by_id", columns={"created_by_id"}),
 *          @ORM\Index(name="updated_by_id", columns={"updated_by_id"}),
 *          @ORM\Index(name="deleted_by_id", columns={"deleted_by_id"}),
 *      }
 *  )
 * @ORM\Entity(
 *      repositoryClass="App\Repository\SensorType",
 *  )
 *
 * @JMS\XmlRoot("sensortype")
 *
 * @package App\Entity
 * @author Jukka Tainio <jukka@tainio.fi>
 */
class SensorType implements EntityInterface
{
    // Traits
    use ORMBehaviors\Blameable;
    use ORMBehaviors\Timestampable;

    /**
     * SensorType id.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "SensorType",
     *      "SensorType.id",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="id",
     *      type="guid",
     *      nullable=false,
     *  )
     * @ORM\Id()
     */
    private $id;

    /**
     * SensorType name.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "SensorType",
     *      "SensorType.name",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="name",
     *      type="string",
     *      length=255,
     *  )
     */
    private $name;

    /**
     * SensorType description.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "SensorType",
     *      "SensorType.description",
     *      "set.DTO",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="description",
     *      type="string",
     *      length=1024,
     *  )
     */
    private $description;

    /**
     * SensorType unit.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "SensorType",
     *      "SensorType.unit",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="unit",
     *      type="string",
     *      length=32,
     *  )
     */
    private $unit;

    /**
     * @var ArrayCollection<SensorData>
     *
     * @JMS\Groups({
     *      "Sensor.datas",
     *  })
     * @JMS\Type("ArrayCollection<App\Entity\SensorData>")
     * @JMS\XmlList(entry = "sensordata")
     *
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\Sensor",
     *      mappedBy="sensortype",
     *      cascade={"all"},
     *  )
     */
    private $sensors;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();

        $this->sensors = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */

    public function getTypeName(): string
    {
        return $this->name;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return SensorType
     */
    public function setName(string $name): SensorType
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set Description
     *
     * @param string $description
     *
     * @return SensorType
     */
    public function setDescription(string $description): SensorType
    {
        $this->description = $description;

        return $this;
    }


    /**
     * Set Unit
     *
     * @param string $unit
     *
     * @return SensorType
     */
    public function setUnit(string $unit): SensorType
    {
        $this->unit = $unit;

        return $this;
    }

}
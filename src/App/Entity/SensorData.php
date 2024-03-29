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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SensorData
 *
 * @ORM\Table(
 *      name="sensordata",
 *      indexes={
 *          @ORM\Index(name="created_by_id", columns={"created_by_id"}),
 *          @ORM\Index(name="updated_by_id", columns={"updated_by_id"}),
 *          @ORM\Index(name="deleted_by_id", columns={"deleted_by_id"}),
 *      }
 *  )
 * @ORM\Entity(
 *      repositoryClass="App\Repository\SensorData",
 *  )
 *
 * @JMS\XmlRoot("sensordata")
 *
 * @package App\Entity
 * @author Jukka Tainio <jukka@tainio.fi>
 */
class SensorData implements EntityInterface
{
    // Traits
    use ORMBehaviors\Blameable;
    use ORMBehaviors\Timestampable;

    /**
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "SensorData",
     *      "SensorData.id",
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
     * @var \App\Entity\Sensor
     *
     * @JMS\Groups({
     *      "SensorData.sensor",
     *  })
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\Sensor",
     *      inversedBy="measurements",
     *      cascade={"all"},
     *  )
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(
     *          name="sensor_id",
     *          referencedColumnName="id",
     *          onDelete="CASCADE"
     *      ),
     *  })
     */
    private $sensor;

    /**
     * @var float
     *
     * @JMS\Groups({
     *      "Default",
     *      "SensorData",
     *      "SensorData.value",
     *  })
     * @JMS\Type("float")
     *
     * @ORM\Column(
     *      name="value",
     *      type="float",
     *  )
     */
    private $value;

    /**
     * SensorData stamp.
     *
     * @var \DateTime
     *
     * @JMS\Groups({
     *      "Default",
     *      "SensorData",
     *      "SensorData.stamp",
     *  })
     * @JMS\Type("DateTime")
     *
     * @ORM\Column(
     *      name="stamp",
     *      type="datetime",
     *  )
     */
    private $stamp;

    /**
     * SensorData constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
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
     * Get value
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Get Stamp
     *
     * @return \DateTime
     */
    public function getStamp(): \DateTime
    {
        return $this->stamp;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return SensorData
     */
    public function setValue(float $value): SensorData
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set Stamp
     *
     * @param \DateTime $stamp
     *
     * @return SensorData
     */
    public function setStamp(\DateTime $stamp): SensorData
    {
        $this->stamp = $stamp;

        return $this;
    }

    /**
     * @return Sensor
     */
    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    /**
     * @param Sensor $sensor
     *
     * @return SensorData
     */
    public function setSensor(Sensor $sensor): SensorData
    {
        $this->sensor = $sensor;

        return $this;
    }
}

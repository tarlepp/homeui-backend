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


class SensorData implements EntityInterface
{
    // Traits
    use ORMBehaviors\Blameable;
    use ORMBehaviors\Timestampable;

    /**
     * SensorData id.
     *
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
     * SensorData Sensor id.
     *
     * @var ArrayCollection<Sensor>
     *
     * @JMS\Groups({
     *      "SensorData",
     *      "SensorData.sensorid",
     *  })
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\Sensor",
     *      mappedBy="id",
     *  )
     * @JMS\XmlList(entry = "SensorData")
     *
     * @ORM\Column(
     *      name="sensorid",
     *      type="string",
     *      lenght=255,
     *  )
     */
    private $sensorid;

    /**
     * SensorData value.
     *
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
     * @var datetime
     *
     * @JMS\Groups({
     *      "Default",
     *      "SensorData",
     *      "SensorData.timestamp",
     *  })
     * @JMS\Type("datetime")
     *
     * @ORM\Column(
     *      name="value",
     *      type="datetime",
     *  )
     */
    private $stamp;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();

        $this->SensorData = new ArrayCollection();
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
     * Get sensor_type_id
     *
     * @return string
     */

    public function getSensorId(): string
    {
        return $this->sensorid;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Get Stamp
     *
     * @return datetime
     */
    public function getStamp(): datetime
    {
        return $this->stamp;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return SensorData
     */
    public function setValue(float $value): SensorData
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Set Stamp
     *
     * @param datetime $stamp
     *
     * @return SensorData
     */
    public function setStamp(datetime $stamp): SensorData
    {
        $this->stamp = $stamp;

        return $this;
    }

}
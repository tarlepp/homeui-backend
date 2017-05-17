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
     * Sensor id.
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
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\SensorData",
     *      mappedBy="Sensor_ID",
     *      cascade={"all"},
     *  )
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
     * Sensor sensor_type_id.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "Sensor",
     *      "Sensor.sensor_type_id",
     *      "set.DTO",
     *  })
     * @JMS\Type("string")
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @ORM\Column(
     *      name="sensor_type_id",
     *      type="guid",
     *      nullable=false,
     *  )
     */
    private $sensor_type_id;

    /**
     * Sensor name.
     *
     * @var ArrayCollection<Sensor>
     *
     * @JMS\Groups({
     *      "Sensor",
     *      "Sensor.name",
     *  })
     * @JMS\Type("ArrayCollection<App\Entity\SensorData>")
     * @JMS\XmlList(entry = "Sensor")
     *
     * @ORM\Column(
     *      name="name",
     *      type="string",
     *      lenght=255,
     *  )
     */
    private $name;

    /**
     * Sensor description.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "Sensor",
     *      "Sensor.description",
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
     * Sensor IP-address.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "Sensor",
     *      "Sensor.ip",
     *      "set.DTO",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="description",
     *      type="string",
     *      length=255,
     *  )
     */
    private $ip;

    /**
     * Sensor SNMP-OID.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "Sensor",
     *      "Sensor.snmp_oid",
     *      "set.DTO",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="description",
     *      type="string",
     *      length=255,
     *  )
     */
    private $snmp_oid;

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

    public function getSensorTypeId(): string
    {
        return $this->sensor_type_id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get IP-address
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get SNMP-OID
     *
     * @return string
     */
    public function getSnmpOid()
    {
        return $this->snmp_oid;
    }


    /**
     * Get sensor data
     *
     * @return ArrayCollection<SensorData>
     */
    public function getSensorData(): ArrayCollection
    {
        return $this->SensorData;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Sensor
     */
    public function setName(string $name): Sensor
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Sensor
     */
    public function setDescription(string $description): Sensor
    {
        $this->description = $description;

        return $this;
    }

}
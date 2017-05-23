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
 * Sensor
 *
 * @ORM\Table(
 *      name="sensor",
 *      indexes={
 *          @ORM\Index(name="created_by_id", columns={"created_by_id"}),
 *          @ORM\Index(name="updated_by_id", columns={"updated_by_id"}),
 *          @ORM\Index(name="deleted_by_id", columns={"deleted_by_id"}),
 *      }
 *  )
 * @ORM\Entity(
 *      repositoryClass="App\Repository\Sensor",
 *  )
 *
 * @JMS\XmlRoot("sensor")
 *
 * @package App\Entity
 * @author Jukka Tainio <jukka@tainio.fi>
 */

class Sensor implements EntityInterface
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
     *      "Sensor",
     *      "Sensor.id",
     *      "SensorData.sensorid",
     *  })
     * @JMS\Type("string")
     *
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\SensorData",
     *      mappedBy="sensorid",
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
     * @JMS\Type("string")
     *
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\SensorType",
     *      inversedBy="id",
     *      cascade={"all"},
     *  )
     *
     * @ORM\Column(
     *      name="name",
     *      type="string",
     *      length=255,
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
     *      name="ip",
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
     *      name="snmp_oid",
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get IP-address
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Get SNMP-OID
     *
     * @return string
     */
    public function getSnmpOid(): string
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
     * Set sensor_type_id
     *
     * @param string $sensor_type_id
     *
     * @return Sensor
     */
    public function setSensorTypeId(string $sensor_type_id): Sensor
    {
        $this->name = $sensor_type_id;

        return $this;
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

    /**
     * Set IP
     *
     * @param string $ip
     *
     * @return Sensor
     */
    public function setIp(string $ip): Sensor
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set snmp_oid
     *
     * @param string $snmp_oid
     *
     * @return Sensor
     */
    public function setSnmpOid(string $snmp_oid): Sensor
    {
        $this->snmp_oid = $snmp_oid;

        return $this;
    }


}
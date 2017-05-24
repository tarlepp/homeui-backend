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
     *      "SensorData.sensor",
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
     *      "Sensor.sensorType",
     *  })
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\SensorType",
     *      inversedBy="sensors",
     *      cascade={"all"},
     *  )
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(
     *          name="sensor_type_id",
     *          referencedColumnName="id",
     *          onDelete="CASCADE"
     *      ),
     *  })
     */
    private $sensorType;

    /**
     * Sensor name.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Sensor",
     *      "Sensor.name",
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
     * Sensor description.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "Sensor",
     *      "Sensor.description",
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
     *  })
     * @JMS\Type("string")
     *
     * @ORM\Column(
     *      name="snmp_oid",
     *      type="string",
     *      length=255,
     *  )
     */
    private $snmpOid;

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
     *      targetEntity="App\Entity\SensorData",
     *      mappedBy="sensor",
     *      cascade={"all"},
     *  )
     */
    private $measurements;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();

        $this->measurements = new ArrayCollection();
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

    public function getSensorType(): string
    {
        return $this->sensorType;
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
        return $this->snmpOid;
    }

    /**
     * @param SensorType $sensorType
     *
     * @return Sensor
     */
    public function setSensorType(SensorType $sensorType): Sensor
    {
        $this->sensorType = $sensorType;

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
     * @param string $snmpOid
     *
     * @return Sensor
     */
    public function setSnmpOid(string $snmpOid): Sensor
    {
        $this->snmpOid = $snmpOid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMeasurements()
    {
        return $this->measurements;
    }
}
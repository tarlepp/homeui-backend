<?php
declare(strict_types=1);
/**
 * /src/App/Command/Migration/WorkCommand.php
 *
 * @author Jukka Tainio <jukka@tainio.fi>
 */
namespace App\Command\Migration;

use App\Entity\Sensor;
use App\Entity\SensorData;
use App\Entity\SensorType;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SensorTableCommand
 *
 * @package App\Command\Migration
 * @author  Jukka Tainio <jukka@tainio.fi>
 *
 */
class SensorCommand extends Base {
    /**
     * Name of the console command.
     *
     * @var string
     */
    protected static $commandName = 'migration:sensor';

    /**
     * Description of the console command.
     *
     * @var string
     */
    protected static $commandDescription = 'Command to migrate old \'Sensor/SensorData/SensorType\' table data to new structure';

    private $cntSensorType = 0;
    private $cntSensor = 0;
    private $cntMeasurement = 0;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return static::$commandDescription;
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        // Initialize common console command
        parent::execute($input, $output);

        if ($input->isInteractive()
            && !$this->io->confirm('ARE YOU ABSOLUTELY SURE THAT YOU WANT RUN THIS MIGRATION?', false)
        ) {
            return 0;
        }

        // Initialize progress bar
        $this->getProgressBar($this->getSourceDataCount('Sensor_Data') + 1);

        // Truncate SensorTable entity table
        $this->truncateEntity(Sensor::class);
        $this->truncateEntity(SensorData::class);
        $this->truncateEntity(SensorType::class);

        \array_map([$this, 'processSensorType'], $this->getSourceData('Sensor_Type'));

        /**
        // Get used services
        $serviceSensor = $this->getContainer()->get('app.services.rest.sensor');
        $serviceSensorData = $this->getContainer()->get('app.services.rest.sensordata');
        $serviceSensorType = $this->getContainer()->get('app.services.rest.sensortype');

        foreach ($this->getSourceData('Sensor_Type') as $typeRow)
        {

            $sensorType = new SensorType();
            $sensorType->setName($typeRow['Name']);
            $sensorType->setDescription($typeRow['Description']);
            $sensorType->setUnit($typeRow['Unit']);

            // Store entity to database
            $serviceSensorType->save($sensorType);

            // Iterate source data
            foreach ($this->getQueryResults("Select * From Sensor WHERE Sensor_Type_ID=".$typeRow['ID']."") as $row) {

                // Create new Sensor entity
                $sensor = new Sensor();
                $sensor->setSensorType($sensorType);
                $sensor->setName($row['Name']);
                $sensor->setDescription($row['Description']);
                $sensor->setIp($row['IP']);
                $sensor->setSnmpOid($row['Snmp_oid']);

                // Store entity to database
                $serviceSensor->save($sensor);


                // test

                foreach ($this->getQueryResults("Select ID,Value,Stamp From Sensor_Data WHERE Sensor_ID=".$row['ID']."") as $dataRow) {
                    // Create new SensorData entity
                    $date = new \DateTime($dataRow['Stamp']);
                    $sensorData = new SensorData();
                    $sensorData->setStamp($date);
                    $sensorData->setSensor($sensor);
                    $sensorData->setValue($dataRow['Value']);

                    // Store entity to database
                    $serviceSensorData->save($sensorData);

                    $this->progress->advance();

                }

                $count++;
            }

            // And detach entity from entity manager
            $serviceSensorType->getEntityManager()->detach($sensorType);

            // And detach entity from entity manager
            $serviceSensor->getEntityManager()->detach($sensor);

            // And detach entity from entity manager
            $serviceSensorData->getEntityManager()->detach($sensorData);

        }
        */

        $this->em->flush();

        $this->progress->finish();

        $this->io->newLine(2);
        $this->io->success([
            'Successfully created following',
            ' SensorType entities:  ' . $this->cntSensorType,
            ' Sensor entities:      ' . $this->cntSensor,
            ' Measurement entities: ' . $this->cntMeasurement,
        ]);

        return 0;
    }

    /**
     * @param array $sensorTypeRow
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function processSensorType(array $sensorTypeRow): void
    {
        // Create new SensorType entity
        $sensorType = new SensorType();
        $sensorType->setName($sensorTypeRow['Name']);
        $sensorType->setDescription($sensorTypeRow['Description']);
        $sensorType->setUnit($sensorTypeRow['Unit']);

        $this->cntSensorType++;

        // Process current sensor type sensors
        $this->processSensors($sensorType, (int)$sensorTypeRow['ID']);

        // Persist and flush sensor type to database
        $this->em->persist($sensorType);

        // Detach SensorType entity from em
        $this->em->detach($sensorType);
    }

    /**
     * Method to process specified sensor type sensors.
     *
     * @param SensorType $sensorType
     * @param int        $sensorTypeId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function processSensors(SensorType $sensorType, int $sensorTypeId): void
    {
        $query = \sprintf(
            'SELECT * FROM Sensor WHERE Sensor_Type_ID = %d',
            $sensorTypeId
        );

        $sensors = $this->getQueryResults($query);

        \array_map([$this, 'processSensor'], $sensors, \array_fill(0, \count($sensors), $sensorType));
    }

    /**
     * Method to process single sensor
     *
     * @param array      $sensorRow
     * @param SensorType $sensorType
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function processSensor(array $sensorRow, SensorType $sensorType): void
    {
        // Create new Sensor entity
        $sensor = new Sensor();
        $sensor->setSensorType($sensorType);
        $sensor->setName($sensorRow['Name']);
        $sensor->setDescription($sensorRow['Description']);
        $sensor->setIp($sensorRow['IP']);
        $sensor->setSnmpOid($sensorRow['Snmp_oid']);

        // Process current sensor type sensors
        $this->processMeasurements($sensor, (int)$sensorRow['ID']);

        $this->em->persist($sensor);

        $this->cntSensor++;

        // Detach SensorType entity from em
        //$this->em->detach($sensor);
    }

    /**
     * @param Sensor $sensor
     * @param int    $sensorId
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function processMeasurements(Sensor $sensor, int $sensorId): void
    {
        $limit = 1000;
        $offset = 0;

        while (true) {
            $query = \sprintf(
                'SELECT ID, Value, Stamp FROM Sensor_Data WHERE Sensor_ID = %d LIMIT %d OFFSET %d',
                $sensorId,
                $limit,
                $offset
            );

            $measurements = $this->getQueryResults($query);

            // There is no more measurement rows to process
            if (\count($measurements) === 0) {
                break;
            }

            \array_map([$this, 'processMeasurement'], $measurements, \array_fill(0, \count($measurements), $sensor));

            $offset += $limit;
        }
    }

    /**
     * @param array  $measurementRow
     * @param Sensor $sensor
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    private function processMeasurement(array $measurementRow, Sensor $sensor): void
    {
        // Hox I highly recommend that you use UTC date times
        $date = new \DateTime($measurementRow['Stamp']);

        $measurement = new SensorData();
        $measurement->setStamp($date);
        $measurement->setSensor($sensor);
        $measurement->setValue((float)$measurementRow['Value']);

        $this->em->persist($measurement);
        $this->progress->advance();

        $this->cntMeasurement++;
    }
}
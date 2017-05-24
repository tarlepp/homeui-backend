<?php
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
    protected $commandName = 'migration:sensor';

    /**
     * Description of the console command.
     *
     * @var string
     */
    protected $commandDescription = 'Command to migrate old \'Sensor/SensorData/SensorType\' table data to new structure';

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
        $this->truncateEntity('App\Entity\Sensor');
        $this->truncateEntity('App\Entity\SensorData');
        $this->truncateEntity('App\Entity\SensorType');

        // Get used services
        $serviceSensor= $this->getContainer()->get('app.services.rest.sensor');
        $serviceSensorData= $this->getContainer()->get('app.services.rest.sensordata');
        $serviceSensorType= $this->getContainer()->get('app.services.rest.sensortype');

        // Initialize work table data cache
        $cacheSensorTable = [];

        $count = 0;

        foreach ($this->getSourceData('Sensor_Type') as $typeRow)
        {

            $sensorType = new SensorType();
            $sensorType->setName($typeRow['Name']);
            $sensorType->setDescription($typeRow['Description']);
            $sensorType->setUnit($typeRow['Unit']);

            // Store entity to database
            $serviceSensorType->save($sensorType);

            // And detach entity from entity manager
            $serviceSensorType->getEntityManager()->detach($sensorType);

            // Iterate source data
            foreach ($this->getQueryResults("Select * From Sensor WHERE Sensor_Type_ID=".$typeRow['ID']."") as $row) {

                // Create new Sensor entity
                $sensor = new Sensor();
                $sensor->setSensorType($sensorType->getId());
                $sensor->setName($row['Name']);
                $sensor->setDescription($row['Description']);
                $sensor->setIp($row['IP']);
                $sensor->setSnmpOid($row['Snmp_oid']);

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

                    // And detach entity from entity manager
                    $serviceSensorData->getEntityManager()->detach($sensorData);

                    $this->progress->advance();

                }

                // Store entity to database
                $serviceSensor->save($sensor);

                // And detach entity from entity manager
                $serviceSensor->getEntityManager()->detach($sensor);

                $count++;
            }

        }
        $this->progress->finish();

        $this->io->newLine(2);
        $this->io->success('Successfully created total of ' . $count . ' \'Sensor\' entities.');

        return 0;
    }
}
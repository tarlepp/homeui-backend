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
class SensorDataCommand extends Base {
    /**
     * Name of the console command.
     *
     * @var string
     */
    protected static $commandName = 'migration:sensor-data';

    /**
     * Description of the console command.
     *
     * @var string
     */
    protected static $commandDescription = 'Command to migrate old \'SensorData\' table data to new structure';

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
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
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

        /** @var \App\Services\Rest\Sensor $service */
        $service = $this->getContainer()->get('app.services.rest.sensor');

        // Initialize progress bar
        $this->getProgressBar($service->count() + 1);

        // Truncate Sensor and SensorType entity data
        $this->truncateEntity(SensorData::class);

        \array_map([$this, 'processMeasurements'], $service->find());

        $this->progress->finish();

        $this->io->newLine(2);
        $this->io->success([
            'Successfully created following',
            ' Measurement entities: ' . $this->cntMeasurement,
        ]);

        return 0;
    }

    /**
     * @param Sensor $sensor
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function processMeasurements(Sensor $sensor): void
    {
        $limit = 1000;
        $offset = 0;

        while (true) {
            $query = \sprintf(
                'SELECT ID, Value, Stamp FROM Sensor_Data WHERE Sensor_ID = %d LIMIT %d OFFSET %d',
                $sensor->getOldId(),
                $limit,
                $offset
            );

            $measurements = $this->getQueryResults($query);

            // There is no more measurement rows to process
            if (\count($measurements) === 0) {
                $this->em->flush();

                break;
            }

            \array_map([$this, 'processMeasurement'], $measurements, \array_fill(0, \count($measurements), $sensor));

            $this->em->flush();

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
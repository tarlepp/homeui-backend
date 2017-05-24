<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: jukka
 * Date: 19/05/2017
 * Time: 14.45
 */

namespace App\Command\Utils;

use App\Entity\DateDimension;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;


/**
 * Class ConvertHomeuiDatabase
 *
 * @package App\Command\Utils
 * @author  Jukka Tainio <jukka@tainio.fi>
 *
 */
class ConvertHomeuiDatabaseCommand extends ContainerAwareCommand
{

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        // Configure command
        $this
            ->setName('utils:convertHomeuiDatabase')
            ->setDescription('Console command to copy and convert data from old style HomeUI database. <info>WARNING! This clears ALL data from sensors and sensordata -tables. Use with caution!</>')
        ;
    }

    /**
     * Executes the current command.
     *
     * @throws  \Exception
     * @throws  \LogicException
     * @throws  \RuntimeException
     * @throws  OptimisticLockException
     * @throws  ORMInvalidArgumentException
     * @throws  ServiceCircularReferenceException
     * @throws  ServiceNotFoundException
     *
     * @param   InputInterface $input An InputInterface instance
     * @param   OutputInterface $output An OutputInterface instance
     *
     * @return  void
     *
     */

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create output decorator helpers for the Symfony Style Guide.
        $this->io = new SymfonyStyle($input, $output);

        // Set title
        $this->io->title($this->getDescription());

        // Determine start and end years
        $dbHost = $this->getDbHost();
        $dbUser = $this->getDbUser();
        $dbPass = $this->getDbPass();
        $dbName = $this->getDbName();

        // Create actual entities
        $this->copyEntities($dbHost, $dbName, $dbUser, $dbPass);

        $this->io->success('All done - have a nice day!');
    }

    /**
     * Method to get database hostname from user.
     *
     * @throws \RuntimeException
     *
     * @param mixed $hostname
     *
     * @return string
     */
    private function getDbHost(): string
    {
        /**
         * Lambda validator function for start year io question.
         *
         * @param mixed $hostname
         *
         * @return string
         */
        $validator = function ($hostname) {


            if(preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $hostname) //valid chars check
            && preg_match("/^.{1,253}$/", $hostname) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $hostname))
                {
                    return $hostname;
                }
                else {
                    throw new \RuntimeException('Origin database hostname is not valid');
                }
        };

        return (string)$this->io->ask('Origin database hostname ', 'localhost', $validator);
    }

    /**
     * Method to get database username from user.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getDbUser(): string
    {
        /**
         * Lambda validator function for end year io question.
         *
         * @param mixed $dbuser
         *
         * @return int
         */
        $validator = function ($dbuser) {
            $dbuser = (string)$dbuser;

            if(!empty($dbuser) )
            {
                return $dbuser;
            }
            else {
                throw new \RuntimeException('Origin database user required');
            }
        };

        return (string)$this->io->ask('Origin database username ', 'dbuser', $validator);
    }

    /**
     * Method to get database password from user.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getDbPass(): string
    {
        /**
         * Lambda validator function for end year io question.
         *
         * @param mixed $dbpass
         *
         * @return int
         */
        $validator = function ($dbpass) {
            $dbpass = (string)$dbpass;

            if(!empty($dbpass) )
            {
                return $dbpass;
            }
            else {
                throw new \RuntimeException('Origin database password required');
            }
        };

        return (string)$this->io->ask('Origin database password ', 'dbpass', $validator);
    }

    /**
     * Method to get database name from user.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getDbName(): string
    {
        /**
         * Lambda validator function for end year io question.
         *
         * @param mixed $dbname
         *
         * @return int
         */
        $validator = function ($dbname) {
            $dbname = (string)$dbname;

            if(!empty($dbname) )
            {
                return $dbname;
            }
            else {
                throw new \RuntimeException('Origin database name required');
            }
        };

        return (string)$this->io->ask('Origin database name ', 'mydb', $validator);
    }

    private function getSourceDataCount($tableName)
    {
        if (is_array($tableName)) {
            $output = 0;

            foreach ($tableName as $table) {
                $output += $this->getSourceDataCount($table);
            }

            return $output;
        } else {
            return (int)$this->getConnection()->query('SELECT COUNT(*) FROM ' . $tableName)->fetchColumn();
        }
    }

    /**
     * Method to create DateDimension entities to database.
     *
     * @throws \Exception
     * @throws \LogicException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws OptimisticLockException
     * @throws ORMInvalidArgumentException
     *
     * @param int $yearStart
     * @param int $yearEnd
     */
    private function copyEntities(string $dbHost, $dbName, $dbUser, $dbPass)
    {
        $sensorCount=$this->getSourceDataCount('Sensor');
        $sensorDataCount=$this->getSourceDataCount('Sensor_Data');
        $progress = $this->getProgressBar(
            (int)$dateEnd->diff($dateStart)->format('%a') + 1,
            \sprintf('Copying HomeUI database %d and %d...', $yearStart, $yearEnd)
        );

        // Get repository
        $repository = $this->getContainer()->get('repository.sensor');

        // Remove existing entities
        $repository->reset();

        // Get entity manager for _fast_ database handling.
        $em = $repository->getEntityManager();

        // Initialize used temp variable
        $currentYear = $yearStart;

        // You spin me round (like a record... er like a date)
        while (true) {
            // All done break the loop
            if ((int)$dateStart->format('Y') > $yearEnd) {
                break;
            }

            // Flush whole year of entities at one time
            if ($currentYear !== (int)$dateStart->format('Y')) {
                $em->flush();
                $em->clear();

                $currentYear = (int)$dateStart->format('Y');
            }

            // Persist entity, advance progress bar and move to next date
            $em->persist(new DateDimension(clone $dateStart));
            $progress->advance();
            $dateStart->add(new \DateInterval('P1D'));
        }

        // Finally flush remaining entities
        $em->flush();
        $em->clear();
    }

    /**
     * Helper method to get progress bar for console.
     *
     * @param   int     $steps
     * @param   string  $message
     *
     * @return  ProgressBar
     */
    private function getProgressBar(int $steps, string $message): ProgressBar
    {
        $format = '
 %message%
 %current%/%max% [%bar%] %percent:3s%%
 Time elapsed:   %elapsed:-6s%
 Time remaining: %remaining:-6s%
 Time estimated: %estimated:-6s%
 Memory usage:   %memory:-6s%
';

        $progress = $this->io->createProgressBar($steps);
        $progress->setFormat($format);
        $progress->setMessage($message);

        return $progress;
    }
}

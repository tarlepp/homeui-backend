<?php
/**
 * /src/App/Command/Migration/Base.php
 *
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
namespace App\Command\Migration;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Base
 *
 * @package App\Command\Migration
 * @author  Jukka Tainio <jukka@tainio.fi>
 */
abstract class Base extends ContainerAwareCommand
{
    /**
     * Name of the console command.
     *
     * @var string
     */
    protected $commandName;

    /**
     * Description of the console command.
     *
     * @var string
     */
    protected $commandDescription;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Supported command line parameters. This is an array that contains array configuration of each parameter,
     * following structure is supported.
     *
     *  [
     *      'name'          => '', // The option name
     *      'shortcut'      => '', // The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     *      'mode'          => '', // The option mode: One of the InputOption::VALUE_* constants
     *      'description'   => '', // A description text
     *      'default'       => '', // The default value (must be null for InputOption::VALUE_NONE)
     *  ]
     *
     * @var array
     */
    protected $commandParameters = [];

    /**
     * Default parameters for migration commands.
     *
     * @var array
     */
    protected $defaultParameters = [
        [
            'name'          => 'hostname',
            'description'   => 'Hostname',
        ],
        [
            'name'          => 'database',
            'description'   => 'Database',
        ],
        [
            'name'          => 'username',
            'description'   => 'Username',
        ],
        [
            'name'          => 'password',
            'description'   => 'Password',
        ],
    ];

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var string
     */
    protected $hostname;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var ProgressBar
     */
    protected $progress;

    /**
     * @var bool
     */
    protected $showConfirm = true;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        /**
         * Lambda iterator function to parse specified inputs.
         *
         * @param   array   $input
         *
         * @return  InputOption
         */
        $iterator = function(array $input) {
            return new InputOption(
                $input['name'],
                array_key_exists('shortcut', $input)    ? $input['shortcut']    : null,
                array_key_exists('mode', $input)        ? $input['mode']        : InputOption::VALUE_OPTIONAL,
                array_key_exists('description', $input) ? $input['description'] : '',
                array_key_exists('default', $input)     ? $input['default']     : null
            );
        };

        // Configure command
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->setDefinition(
                new InputDefinition(
                    array_map($iterator, array_merge($this->defaultParameters, $this->commandParameters))
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Store input and output objects
        $this->input = $input;
        $this->output = $output;

        // Get entity manager
        $this->em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        // Create output decorator helpers for the Symfony Style Guide.
        $this->io = new SymfonyStyle($this->input, $this->output);

        // Store database connection variables
        $this->hostname = $this->input->getOption('hostname');
        $this->database = $this->input->getOption('database');
        $this->username = $this->input->getOption('username');
        $this->password = $this->input->getOption('password');

        // Create connection to source database, this is just to make sure that given parameters works
        $this->getConnection();

        // Set title
        $this->io->title($this->getDescription());
    }

    /**
     * Getter method for progress bar object.
     *
     * @param   integer $steps
     *
     * @return  ProgressBar
     */
    protected function getProgressBar($steps = 0)
    {
        // Create new progress bar and start it
        if (!($this->progress instanceof ProgressBar)) {
            $this->io->newLine(4);

            $this->progress = $this->io->createProgressBar($steps);
            $this->progress->setBarWidth(48);
            $this->progress->start();
            $this->progress->setFormat('Processing command: ' . $this->commandName . '  
 %current%/%max% [%bar%] %percent:3s%% 
 Elapsed time:   %elapsed:6s%
 Estimated time: %estimated:-6s% 
 Memory usage:   %memory:6s%'
            );
        }

        return $this->progress;
    }

    /**
     * Getter method for source database connection.
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function getConnection()
    {
        if (is_null($this->connection)) {
            $connectionFactory = $this->getContainer()->get('doctrine.dbal.connection_factory');

            // Create new connection
            $this->connection = $connectionFactory->createConnection([
                'driver'    => 'pdo_mysql',
                'user'      => $this->username,
                'password'  => $this->password,
                'host'      => $this->hostname,
                'dbname'    => $this->database,
            ]);

            // Ping current connection, this is to make sure that we have connection to source database
            $this->connection->ping();

            // We want to use data as UTF8 format
            $this->connection->executeQuery('SET NAMES UTF8');
        }

        return $this->connection;
    }

    /**
     * Getter method for table name from entity class.
     *
     * @param   string  $entityName
     *
     * @return  string
     */
    protected function getTableName($entityName)
    {
        return $this->em->getClassMetadata($entityName)->getTableName();
    }

    /**
     * Getter method for source database table data.
     *
     * @throws  \Doctrine\DBAL\DBALException
     *
     * @param   string  $tableName
     *
     * @return  array
     */
    protected function getSourceData($tableName)
    {
        return $this->getConnection()->query('SELECT * FROM ' . $tableName)->fetchAll();
    }

    /**
     * Getter method for specified query results - multiple rows.
     *
     * @throws  \Doctrine\DBAL\DBALException
     *
     * @param   string  $query
     *
     * @return  array
     */
    protected function getQueryResults($query)
    {
        return $this->getConnection()->query($query)->fetchAll();
    }

    /**
     * Getter method for specified query result - one row.
     *
     * @throws  \Doctrine\DBAL\DBALException
     *
     * @param   string  $query
     *
     * @return  mixed
     */
    protected function getQueryResult($query)
    {
        return $this->getConnection()->query($query)->fetch();
    }

    /**
     * Getter method for simple data count for specified table.
     *
     * @throws  \Doctrine\DBAL\DBALException
     *
     * @param   $tableName
     *
     * @return  integer
     */
    protected function getSourceDataCount($tableName)
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
     * @param   string $className
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function truncateEntity($className)
    {
        // Get necessary information
        $metadata = $this->em->getClassMetadata($className);
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();

        // Truncate entity table
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeUpdate($platform->getTruncateTableSQL($metadata->getTableName()));
        $connection->query('SET FOREIGN_KEY_CHECKS=1');

        if ($this->progress instanceof ProgressBar) {
            $this->progress->advance();
        }
    }
}
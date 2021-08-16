<?php
declare(strict_types=1);

namespace Mfc\Dbal\Cargo\Transaction;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\RetryableException;
use Mfc\Dbal\Cargo\Connection\ConnectionFactoryInterface;
use Mfc\Dbal\Cargo\Database\MetadataProviderInterface;
use Mfc\Dbal\Cargo\Transaction\Exception\TransactionFailedException;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractTransaction
 * @package Pfmmedical\PfmmedicalSitepackage\Import\Transaction
 * @author Christian Spoo <christian.spoo@marketing-factory.de>
 */
abstract class AbstractTransaction
{
    /**
     * @var Connection
     */
    protected Connection $databaseConnection;
    /**
     * @var MetadataProviderInterface
     */
    protected MetadataProviderInterface $metadataProvider;
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;
    /**
     * @var bool
     */
    private static bool $useLogging = false;

    /**
     * @return void
     */
    public static function enableLogging(): void
    {
        self::$useLogging = true;
    }

    /**
     * @return void
     */
    public static function disableLogging(): void
    {
        self::$useLogging = false;
    }

    /**
     * @param ConnectionFactoryInterface $connectionFactory
     * @param MetadataProviderInterface $metadataProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionFactoryInterface $connectionFactory,
        MetadataProviderInterface $metadataProvider,
        LoggerInterface $logger
    ) {
        $this->databaseConnection = $connectionFactory->getConnection();
        $this->metadataProvider = $metadataProvider;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    abstract protected function executeQueries();

    /**
     * @throws RetryableException
     * @throws TransactionFailedException
     */
    public function execute()
    {
        if (self::$useLogging) {
            $this->logger->debug(static::class . ' (Begin)');
        }
        try {
            $this->executeQueries();

            if (self::$useLogging) {
                $this->logger->debug(static::class . ' (Commit)');
            }
        } catch (RetryableException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('DBAL exception caught: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            $this->logger->error('Transaction ' . static::class . ' failed', [ 'transaction' => __CLASS__ ]);

            $originalException = $e;
            do {
                $this->logger->error(strtok($e->getMessage(), PHP_EOL));
                $this->logger->debug(
                    get_class($e)
                    . ' thrown in ' . $e->getFile()
                    . ' @ ' . $e->getLine()
                    . PHP_EOL . $e->getTraceAsString()
                );

                $e = $e->getPrevious();
            } while ($e->getPrevious() instanceof \Exception);

            $this->logger->info(static::class . ' (Rollback)');

            throw new TransactionFailedException('Transaction failed', 1503647119, $originalException);
        }
    }

    /**
     * @param string $sql
     * @return void
     */
    protected function logSql(string $sql): void
    {
        if (!self::$useLogging) {
            return;
        }

        $this->logger->info($sql);
    }}

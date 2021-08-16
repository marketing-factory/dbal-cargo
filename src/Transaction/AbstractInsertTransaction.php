<?php
declare(strict_types=1);

namespace Mfc\Dbal\Cargo\Transaction;

use Mfc\Dbal\Cargo\Connection\ConnectionFactoryInterface;
use Mfc\Dbal\Cargo\Database\MetadataProviderInterface;
use Mfc\Dbal\Cargo\Transaction\Exception\UndefinedLastInsertIdException;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractInsertTransaction
 * @package Mfc\Dbal\Cargo\Transaction
 * @author Christian Spoo <christian.spoo@marketing-factory.de>
 */
abstract class AbstractInsertTransaction extends AbstractTransaction
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $lastInsertId;

    /**
     * @param ConnectionFactoryInterface $connectionFactory
     * @param MetadataProviderInterface $metadataProvider
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        ConnectionFactoryInterface $connectionFactory,
        MetadataProviderInterface $metadataProvider,
        LoggerInterface $logger,
        array $data
    ) {
        parent::__construct($connectionFactory, $metadataProvider, $logger);
        $this->data = $data;
    }

    /**
     * @return int
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception\RetryableException
     */
    public function execute()
    {
        parent::execute();

        if ($this->lastInsertId === null) {
            throw new UndefinedLastInsertIdException('Last insert id is undefined', 1503648543);
        }

        return $this->lastInsertId;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param int $lastInsertId
     */
    public function setLastInsertId($lastInsertId)
    {
        $this->lastInsertId = (int)$lastInsertId;
    }
}

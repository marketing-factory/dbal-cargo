<?php
declare(strict_types=1);

namespace Mfc\Dbal\Cargo\Connection;

use Doctrine\DBAL\Connection;

/**
 * Interface ConnectionFactoryInterface
 * @package Mfc\Dbal\Cargo\Connection
 * @author Christian Spoo <christian.spoo@marketing-factory.de>
 */
interface ConnectionFactoryInterface
{
    /**
     * @return Connection
     */
    public function getConnection(): Connection;
}

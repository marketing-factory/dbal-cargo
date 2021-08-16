<?php
declare(strict_types=1);

namespace Mfc\Dbal\Cargo\Database;

use Doctrine\DBAL\Schema\Table;

/**
 * Interface MetadataProviderInterface
 * @package Mfc\Dbal\Cargo\Database
 * @author Christian Spoo <christian.spoo@marketing-factory.de>
 */
interface MetadataProviderInterface
{
    /**
     * @param string $tableName
     * @return Table
     */
    public function getTableDetails(string $tableName): Table;
}

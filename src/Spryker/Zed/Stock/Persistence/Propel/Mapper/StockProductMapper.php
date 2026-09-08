<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\StockProductTransfer;
use Orm\Zed\Stock\Persistence\Map\SpyStockTableMap;
use Orm\Zed\Stock\Persistence\SpyStockProduct;

class StockProductMapper
{
    /**
     * @uses \Orm\Zed\Stock\Persistence\Map\SpyStockTableMap::COL_UUID
     */
    protected const string COLUMN_UUID = 'uuid';

    /**
     * @param array<\Orm\Zed\Stock\Persistence\SpyStockProduct> $stockProductEntities
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function mapStockProductEntitiesToStockProductTransfers(array $stockProductEntities): array
    {
        $stockProductTransfers = [];
        foreach ($stockProductEntities as $stockProductEntity) {
            $stockProductTransfers[] = $this->mapStockProductEntityToStockProductTransfer(
                $stockProductEntity,
                new StockProductTransfer(),
            );
        }

        return $stockProductTransfers;
    }

    public function mapStockProductEntityToStockProductTransfer(
        SpyStockProduct $stockProductEntity,
        StockProductTransfer $stockProductTransfer
    ): StockProductTransfer {
        $stockProductTransfer->fromArray($stockProductEntity->toArray(), true);
        $stockProductTransfer->setSku($stockProductEntity->getSpyProduct()->getSku());
        $stockProductTransfer->setStockType($stockProductEntity->getStock()->getName());

        // The `uuid` column on `spy_stock` is optional for projects that have not migrated to it yet.
        if ($this->isStockUuidSupported()) {
            $stockProductTransfer->setStockUuid($stockProductEntity->getStock()->getUuid());
        }

        return $stockProductTransfer;
    }

    protected function isStockUuidSupported(): bool
    {
        return SpyStockTableMap::getTableMap()->hasColumn(static::COLUMN_UUID);
    }
}

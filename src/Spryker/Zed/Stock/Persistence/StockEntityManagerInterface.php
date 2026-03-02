<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Persistence;

use Generated\Shared\Transfer\StockTransfer;

interface StockEntityManagerInterface
{
    public function saveStock(StockTransfer $stockTransfer): StockTransfer;

    /**
     * @param int $idStock
     * @param array<int> $storeIds
     *
     * @return void
     */
    public function addStockStoreRelations(int $idStock, array $storeIds): void;

    /**
     * @param int $idStock
     * @param array<int> $storeIds
     *
     * @return void
     */
    public function deleteStockStoreRelations(int $idStock, array $storeIds): void;
}

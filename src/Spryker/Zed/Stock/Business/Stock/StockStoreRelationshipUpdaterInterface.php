<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\Stock;

use Generated\Shared\Transfer\StoreRelationTransfer;

interface StockStoreRelationshipUpdaterInterface
{
    public function updateStockStoreRelationshipsForStock(int $idStock, ?StoreRelationTransfer $storeRelationTransfer): void;
}

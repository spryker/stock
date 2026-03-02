<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\StockProduct;

use Generated\Shared\Transfer\StockTransfer;

interface StockProductUpdaterInterface
{
    public function updateStockProductsRelatedToStock(StockTransfer $stockTransfer): void;
}

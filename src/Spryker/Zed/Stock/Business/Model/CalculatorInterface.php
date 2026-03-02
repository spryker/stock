<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\Model;

use Generated\Shared\Transfer\StoreTransfer;
use Spryker\DecimalObject\Decimal;

interface CalculatorInterface
{
    public function calculateStockForProduct(string $sku): Decimal;

    public function calculateProductStockForStore(string $sku, StoreTransfer $storeTransfer): Decimal;

    public function calculateProductAbstractStockForStore(string $abstractSku, StoreTransfer $storeTransfer): Decimal;
}

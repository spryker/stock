<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Persistence\Propel;

use Orm\Zed\Stock\Persistence\Base\SpyStockProduct as BaseSpyStockProduct;
use Spryker\DecimalObject\Decimal;

/**
 * Skeleton subclass for representing a row from the 'spy_stock_product' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 */
abstract class AbstractSpyStockProduct extends BaseSpyStockProduct
{
    /**
     * @param \Spryker\DecimalObject\Decimal $amount
     *
     * @return void
     */
    public function decrement(Decimal $amount)
    {
        $this->setQuantity(
            (new Decimal($this->getQuantity()))->subtract($amount),
        );
    }

    /**
     * @param \Spryker\DecimalObject\Decimal $amount
     *
     * @return void
     */
    public function increment(Decimal $amount)
    {
        $this->setQuantity(
            (new Decimal($this->getQuantity()))->add($amount),
        );
    }
}

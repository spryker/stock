<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\Model;

interface CalculatorInterface
{

    /**
     * @param string $sku
     *
     * @return int
     */
    public function calculateStockForProduct($sku);

}

<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Stock\Communication\Plugin\Product;

use Generated\Shared\Transfer\ProductConcreteCollectionRequestTransfer;
use Generated\Shared\Transfer\ProductConcreteCollectionResponseTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductExtension\Dependency\Plugin\ProductConcreteCollectionCreateValidatorPluginInterface;

/**
 * @method \Spryker\Zed\Stock\Business\StockBusinessFactory getBusinessFactory()
 */
class StockExistsProductConcreteCollectionCreateValidatorPlugin extends AbstractPlugin implements ProductConcreteCollectionCreateValidatorPluginInterface
{
    /**
     * {@inheritDoc}
     * - Validates the stock names referenced in `ProductConcreteTransfer.stocks`.
     * - Resolves all referenced stock names of the collection with a single query.
     * - Adds an error per concrete product referencing a warehouse (stock) that does not exist.
     * - Returns the response unchanged when no concrete product references a stock.
     *
     * @api
     */
    public function validate(
        ProductConcreteCollectionRequestTransfer $productConcreteCollectionRequestTransfer,
        ProductConcreteCollectionResponseTransfer $productConcreteCollectionResponseTransfer
    ): ProductConcreteCollectionResponseTransfer {
        return $this->getBusinessFactory()
            ->createProductStockValidator()
            ->validateProductConcreteCollection($productConcreteCollectionRequestTransfer, $productConcreteCollectionResponseTransfer);
    }
}

<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Communication\Plugin\Product;

use Generated\Shared\Transfer\ProductConcreteTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Product\Dependency\Plugin\ProductConcretePluginUpdateInterface;

/**
 * @method \Spryker\Zed\Stock\Business\StockFacadeInterface getFacade()
 * @method \Spryker\Zed\Stock\Communication\StockCommunicationFactory getFactory()
 * @method \Spryker\Zed\Stock\StockConfig getConfig()
 * @method \Spryker\Zed\Stock\Persistence\StockQueryContainerInterface getQueryContainer()
 */
class StockProductConcreteCollectionAfterUpdatePlugin extends AbstractPlugin implements ProductConcretePluginUpdateInterface
{
    /**
     * {@inheritDoc}
     * - Persists the stock collection carried by `ProductConcreteTransfer.stocks`.
     * - Deletes the stock product records of the concrete product that are no longer referenced.
     *
     * @api
     */
    public function update(ProductConcreteTransfer $productConcreteTransfer): ProductConcreteTransfer
    {
        $productConcreteTransfer = $this->getFacade()->persistStockProductCollection($productConcreteTransfer);
        $this->getFacade()->deleteOrphanStockProductsForProductConcrete($productConcreteTransfer);

        return $productConcreteTransfer;
    }
}

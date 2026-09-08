<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Stock\Business\Plugin\Product;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Spryker\Zed\Stock\Communication\Plugin\Product\StockProductConcreteCollectionAfterUpdatePlugin;
use SprykerTest\Zed\Stock\StockBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Stock
 * @group Business
 * @group Plugin
 * @group Product
 * @group StockProductConcreteCollectionAfterUpdatePluginTest
 *
 * Add your own group annotations below this line
 */
class StockProductConcreteCollectionAfterUpdatePluginTest extends Unit
{
    protected const int STOCK_QUANTITY = 10;

    protected StockBusinessTester $tester;

    public function testUpdateDeletesOrphanStockProductsNotReferencedInTransfer(): void
    {
        // Arrange
        $sku = sprintf('pxm-stock-au-%s', uniqid());
        $idProduct = $this->createProductConcrete($sku);
        $this->createStockProductForSku($sku);
        $productConcreteTransfer = (new ProductConcreteTransfer())->setIdProductConcrete($idProduct);

        // Act
        (new StockProductConcreteCollectionAfterUpdatePlugin())->update($productConcreteTransfer);

        // Assert
        $this->assertSame(0, SpyStockProductQuery::create()->filterByFkProduct($idProduct)->count());
    }

    protected function createProductConcrete(string $sku): int
    {
        $productAbstractEntity = new SpyProductAbstract();
        $productAbstractEntity->setSku(sprintf('%s-abstract', $sku))->setAttributes('{}')->save();

        $productEntity = new SpyProduct();
        $productEntity
            ->setSku($sku)
            ->setAttributes('{}')
            ->setFkProductAbstract($productAbstractEntity->getIdProductAbstract())
            ->save();

        return $productEntity->getIdProduct();
    }

    protected function createStockProductForSku(string $sku): int
    {
        $stockTransfer = $this->tester->haveStock();
        $stockProductTransfer = (new StockProductTransfer())
            ->setStockType($stockTransfer->getName())
            ->setQuantity(static::STOCK_QUANTITY)
            ->setSku($sku);

        return $this->tester->getFacade()->createStockProduct($stockProductTransfer);
    }
}

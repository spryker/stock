<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Stock\Business\Plugin\Product;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductConcreteCollectionRequestTransfer;
use Generated\Shared\Transfer\ProductConcreteCollectionResponseTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Spryker\Zed\Stock\Communication\Plugin\Product\StockExistsProductConcreteCollectionCreateValidatorPlugin;
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
 * @group StockExistsProductConcreteCollectionCreateValidatorPluginTest
 *
 * Add your own group annotations below this line
 */
class StockExistsProductConcreteCollectionCreateValidatorPluginTest extends Unit
{
    protected const string UNKNOWN_STOCK_NAME = 'non-existent-warehouse';

    protected const string PRODUCT_SKU = 'concrete-sku';

    protected StockBusinessTester $tester;

    public function testValidateReturnsErrorWhenStockNameDoesNotExist(): void
    {
        // Arrange
        $productConcreteCollectionRequestTransfer = $this->createRequestWithStockName(static::UNKNOWN_STOCK_NAME);

        // Act
        $productConcreteCollectionResponseTransfer = (new StockExistsProductConcreteCollectionCreateValidatorPlugin())->validate(
            $productConcreteCollectionRequestTransfer,
            new ProductConcreteCollectionResponseTransfer(),
        );

        // Assert
        $this->assertCount(1, $productConcreteCollectionResponseTransfer->getErrors());
        $this->assertSame(static::PRODUCT_SKU, $productConcreteCollectionResponseTransfer->getErrors()->offsetGet(0)->getEntityIdentifier());
    }

    public function testValidatePassesWhenStockNameExists(): void
    {
        // Arrange
        $stockTransfer = $this->tester->haveStock();
        $productConcreteCollectionRequestTransfer = $this->createRequestWithStockName((string)$stockTransfer->getName());

        // Act
        $productConcreteCollectionResponseTransfer = (new StockExistsProductConcreteCollectionCreateValidatorPlugin())->validate(
            $productConcreteCollectionRequestTransfer,
            new ProductConcreteCollectionResponseTransfer(),
        );

        // Assert
        $this->assertCount(0, $productConcreteCollectionResponseTransfer->getErrors());
    }

    public function testValidatePassesWhenNoStockNamesProvided(): void
    {
        // Arrange
        $productConcreteCollectionRequestTransfer = (new ProductConcreteCollectionRequestTransfer())
            ->addProduct((new ProductConcreteTransfer())->setSku(static::PRODUCT_SKU));

        // Act
        $productConcreteCollectionResponseTransfer = (new StockExistsProductConcreteCollectionCreateValidatorPlugin())->validate(
            $productConcreteCollectionRequestTransfer,
            new ProductConcreteCollectionResponseTransfer(),
        );

        // Assert
        $this->assertCount(0, $productConcreteCollectionResponseTransfer->getErrors());
    }

    protected function createRequestWithStockName(string $stockName): ProductConcreteCollectionRequestTransfer
    {
        return (new ProductConcreteCollectionRequestTransfer())
            ->addProduct(
                (new ProductConcreteTransfer())
                    ->setSku(static::PRODUCT_SKU)
                    ->addStock((new StockProductTransfer())->setStockType($stockName)),
            );
    }
}

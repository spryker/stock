<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Stock\Business;

use Codeception\Test\Unit;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use Spryker\Zed\Stock\Business\StockFacade;
use Spryker\Zed\Stock\Persistence\StockQueryContainer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Stock
 * @group Business
 * @group ReaderTest
 * Add your own group annotations below this line
 */
class ReaderTest extends Unit
{
    /**
     * @var string
     */
    protected const WAREHOUSE_1 = 'test_warehouse1';

    /**
     * @var string
     */
    protected const WAREHOUSE_2 = 'test_warehouse2';

    /**
     * @var \Spryker\Zed\Stock\Business\StockFacade
     */
    protected $stockFacade;

    /**
     * @var \Spryker\Zed\Stock\Persistence\StockQueryContainer
     */
    protected $stockQueryContainer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->stockFacade = new StockFacade();
        $this->stockQueryContainer = new StockQueryContainer();
    }

    /**
     * @return void
     */
    public function testIsNeverOutOfStock(): void
    {
        $this->setTestData();
        $stockProductEntity = $this->stockQueryContainer->queryAllStockProducts()->findOne();
        $stockProductEntity->setIsNeverOutOfStock(true)->save();
        $productSku = SpyProductQuery::create()->findOneByIdProduct($stockProductEntity->getFkProduct());
        $isNeverOutOfStock = $this->stockFacade->isNeverOutOfStock($productSku->getSku());

        $this->assertTrue($isNeverOutOfStock);
    }

    /**
     * @return void
     */
    protected function setTestData(): void
    {
        $productAbstract = SpyProductAbstractQuery::create()
            ->filterBySku('test')
            ->findOne();

        if ($productAbstract === null) {
            $productAbstract = new SpyProductAbstract();
            $productAbstract->setSku('test');
        }

        $productAbstract->setAttributes('{}')
            ->save();

        $product = SpyProductQuery::create()
            ->filterBySku('test2')
            ->findOne();

        if ($product === null) {
            $product = new SpyProduct();
            $product->setSku('test2');
        }

        $product->setFkProductAbstract($productAbstract->getIdProductAbstract())
            ->setAttributes('{}')
            ->save();

        $stockType1 = SpyStockQuery::create()
            ->filterByName(static::WAREHOUSE_1)
            ->findOneOrCreate();

        $stockType1->setName(static::WAREHOUSE_1)
            ->save();

        $stockType2 = SpyStockQuery::create()
            ->filterByName(static::WAREHOUSE_2)
            ->findOneOrCreate();

        $stockType2->setName(static::WAREHOUSE_2)
            ->save();

        $stockProduct1 = SpyStockProductQuery::create()
            ->filterByFkStock($stockType1->getIdStock())
            ->filterByFkProduct($product->getIdProduct())
            ->findOneOrCreate();

        $stockProduct1->setFkStock($stockType1->getIdStock())
            ->setQuantity(10)
            ->setFkProduct($product->getIdProduct())
            ->save();

        $stockProduct2 = SpyStockProductQuery::create()
            ->filterByFkStock($stockType2->getIdStock())
            ->filterByFkProduct($product->getIdProduct())
            ->findOneOrCreate();

        $stockProduct2->setFkStock($stockType2->getIdStock())
            ->setQuantity(20)
            ->setFkProduct($product->getIdProduct())
            ->save();
    }
}

<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Stock\Business;

use Codeception\Test\Unit;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use Spryker\Zed\Stock\Business\StockFacade;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Stock
 * @group Business
 * @group CalculatorTest
 * Add your own group annotations below this line
 */
class CalculatorTest extends Unit
{
    /**
     * @var string
     */
    public const WAREHOUSE_1 = 'test_warehouse1';

    /**
     * @var string
     */
    public const WAREHOUSE_2 = 'test_warehouse2';

    /**
     * @var \Spryker\Zed\Stock\Business\StockFacade
     */
    protected $stockFacade;

    /**
     * @var \Orm\Zed\Product\Persistence\SpyProduct
     */
    protected $productEntity;

    /**
     * @var \Orm\Zed\Stock\Persistence\SpyStock
     */
    protected $stockEntity1;

    /**
     * @var \Orm\Zed\Stock\Persistence\SpyStock
     */
    protected $stockEntity2;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->stockFacade = new StockFacade();

        $this->setupProductEntity();
        $this->setupStockProductEntity();
    }

    /**
     * @return void
     */
    public function testCalculateStock(): void
    {
        $stock = $this->stockFacade->calculateStockForProduct($this->productEntity->getSku());
        $this->assertTrue($stock->equals(30));
    }

    /**
     * @return void
     */
    public function testCalculateStockWillCalculateStockOnlyInActiveStocks(): void
    {
        //Arrange
        $this->stockEntity2->setIsActive(false)->save();

        //Act
        $stock = $this->stockFacade->calculateStockForProduct($this->productEntity->getSku());

        //Assert
        $this->assertTrue($stock->equals(10));
    }

    /**
     * @return void
     */
    protected function setupProductEntity(): void
    {
        $productAbstract = SpyProductAbstractQuery::create()
            ->filterBySku('test')
            ->findOneOrCreate();

        $productAbstract
            ->setAttributes('{}')
            ->save();

        $product = SpyProductQuery::create()
            ->filterBySku('test2')
            ->findOneOrCreate();

        $product
            ->setFkProductAbstract($productAbstract->getIdProductAbstract())
            ->setAttributes('{}')
            ->save();

        $this->productEntity = $product;

        $this->stockEntity1 = SpyStockQuery::create()
            ->filterByName(static::WAREHOUSE_1)
            ->findOneOrCreate();

        $this->stockEntity1->setName(static::WAREHOUSE_1)->save();

        $this->stockEntity2 = SpyStockQuery::create()
            ->filterByName(static::WAREHOUSE_2)
            ->findOneOrCreate();
        $this->stockEntity2->setName(static::WAREHOUSE_2)->save();

        $stockProduct1 = SpyStockProductQuery::create()
            ->filterByFkStock($this->stockEntity1->getIdStock())
            ->filterByFkProduct($this->productEntity->getIdProduct())
            ->findOneOrCreate();
        $stockProduct1->setFkStock($this->stockEntity1->getIdStock())
            ->setQuantity(10)
            ->setFkProduct($this->productEntity->getIdProduct())
            ->save();

        $stockProduct2 = SpyStockProductQuery::create()
            ->filterByFkStock($this->stockEntity2->getIdStock())
            ->filterByFkProduct($this->productEntity->getIdProduct())
            ->findOneOrCreate();
        $stockProduct2->setFkStock($this->stockEntity2->getIdStock())
            ->setQuantity(20)
            ->setFkProduct($this->productEntity->getIdProduct())
            ->save();
    }

    /**
     * @return void
     */
    protected function setupStockProductEntity(): void
    {
        $this->stockEntity1 = SpyStockQuery::create()
            ->filterByName(static::WAREHOUSE_1)
            ->findOneOrCreate();

        $this->stockEntity1
            ->setName(static::WAREHOUSE_1)->save();

        $this->stockEntity2 = SpyStockQuery::create()
            ->filterByName(static::WAREHOUSE_2)
            ->findOneOrCreate();

        $this->stockEntity2
            ->setName(static::WAREHOUSE_2)->save();

        $stockProduct1 = SpyStockProductQuery::create()
            ->filterByFkStock($this->stockEntity1->getIdStock())
            ->filterByFkProduct($this->productEntity->getIdProduct())
            ->findOneOrCreate();

        $stockProduct1->setFkStock($this->stockEntity1->getIdStock())
            ->setQuantity(10)
            ->setFkProduct($this->productEntity->getIdProduct())
            ->save();

        $stockProduct2 = SpyStockProductQuery::create()
            ->filterByFkStock($this->stockEntity2->getIdStock())
            ->filterByFkProduct($this->productEntity->getIdProduct())
            ->findOneOrCreate();

        $stockProduct2->setFkStock($this->stockEntity2->getIdStock())
            ->setQuantity(20)
            ->setFkProduct($this->productEntity->getIdProduct())
            ->save();
    }
}

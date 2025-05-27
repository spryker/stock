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
use Spryker\DecimalObject\Decimal;
use Spryker\Zed\Stock\Business\StockFacade;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Stock
 * @group Business
 * @group WriterTest
 * Add your own group annotations below this line
 */
class WriterTest extends Unit
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
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->stockFacade = new StockFacade();
    }

    /**
     * @return void
     */
    public function testDecrementStock(): void
    {
        $this->saveData();
        $stockProductEntity = SpyStockProductQuery::create()->findOne();
        $stockProductEntity->reload();
        $oldQuantity = $stockProductEntity->getQuantity();
        $product = SpyProductQuery::create()
            ->findOneByIdProduct($stockProductEntity->getFkProduct());
        $stockType = SpyStockQuery::create()
            ->findOneByIdStock($stockProductEntity->getFkStock());

        $this->stockFacade->decrementStockProduct($product->getSku(), $stockType->getName(), new Decimal(1));

        $stockEntity = SpyStockProductQuery::create()->findOneByIdStockProduct($stockProductEntity->getIdStockProduct());
        $newQuantity = $stockEntity->getQuantity();

        $this->assertSame((new Decimal($oldQuantity))->subtract(1)->toString(), $newQuantity);
    }

    /**
     * @return void
     */
    public function testIncrementStock(): void
    {
        $this->saveData();
        $stockProductEntity = SpyStockProductQuery::create()->findOne();
        $stockProductEntity->reload();
        $oldQuantity = $stockProductEntity->getQuantity();
        $product = SpyProductQuery::create()->findOneByIdProduct($stockProductEntity->getFkProduct());
        $stockType = SpyStockQuery::create()->findOneByIdStock($stockProductEntity->getFkStock());

        $this->stockFacade->incrementStockProduct($product->getSku(), $stockType->getName(), new Decimal(1));

        $stockEntity = SpyStockProductQuery::create()->findOneByIdStockProduct($stockProductEntity->getIdStockProduct());
        $newQuantity = $stockEntity->getQuantity();

        $this->assertSame((new Decimal($oldQuantity))->add(1)->toString(), $newQuantity);
    }

    /**
     * @return void
     */
    protected function saveData(): void
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
        $stockType2->setName(static::WAREHOUSE_2)->save();

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

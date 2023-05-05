<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Stock\Business;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\StockBuilder;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StockCriteriaFilterTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\StockTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\TypeTransfer;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Stock\Persistence\SpyStock;
use Orm\Zed\Stock\Persistence\SpyStockProduct;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use Spryker\DecimalObject\Decimal;
use Spryker\Zed\Stock\Business\Exception\StockProductAlreadyExistsException;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Stock
 * @group Business
 * @group Facade
 * @group StockFacadeTest
 * Add your own group annotations below this line
 */
class StockFacadeTest extends Unit
{
    /**
     * @var string
     */
    protected const STORE_NAME_DE = 'DE';

    /**
     * @var string
     */
    protected const STORE_NAME_AT = 'AT';

    /**
     * @var string
     */
    protected const STOCK_NAME = 'Test Warehouse';

    /**
     * @var int
     */
    protected const INVALID_STOCK_ID = 0;

    /**
     * @var \SprykerTest\Zed\Stock\StockBusinessTester
     */
    protected $tester;

    /**
     * @var \Spryker\Zed\Stock\Business\StockFacade
     */
    protected $stockFacade;

    /**
     * @var \Generated\Shared\Transfer\StoreTransfer
     */
    protected $storeTransfer;

    /**
     * @var \Orm\Zed\Stock\Persistence\SpyStock
     */
    protected $stockEntity1;

    /**
     * @var \Generated\Shared\Transfer\StockTransfer
     */
    protected $stockTransfer1;

    /**
     * @var \Orm\Zed\Stock\Persistence\SpyStock
     */
    protected $stockEntity2;

    /**
     * @var \Generated\Shared\Transfer\StockTransfer
     */
    protected $stockTransfer2;

    /**
     * @var \Orm\Zed\Stock\Persistence\SpyStockProduct
     */
    protected $productStockEntity1;

    /**
     * @var \Orm\Zed\Stock\Persistence\SpyStockProduct
     */
    protected $productStockEntity2;

    /**
     * @var \Orm\Zed\Product\Persistence\SpyProductAbstract
     */
    protected $productAbstractEntity;

    /**
     * @var \Orm\Zed\Product\Persistence\SpyProduct
     */
    protected $productConcreteEntity;

    /**
     * @var string
     */
    public const ABSTRACT_SKU = 'abstract-sku';

    /**
     * @var string
     */
    public const CONCRETE_SKU = 'concrete-sku';

    /**
     * @var int
     */
    public const STOCK_QUANTITY_1 = 92;

    public const STOCK_QUANTITY_2 = 8.2;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->stockFacade = $this->tester->getFacade();
        $this->setupData();
    }

    /**
     * @return void
     */
    public function testIsNeverOutOfStockShouldReturnFalse(): void
    {
        $isNeverOutOfStock = $this->stockFacade->isNeverOutOfStock(static::CONCRETE_SKU);

        $this->assertFalse($isNeverOutOfStock);
    }

    /**
     * @return void
     */
    public function testIsNeverOutOfStockShouldReturnTrue(): void
    {
        $this->productStockEntity1->setIsNeverOutOfStock(true);
        $this->productStockEntity1->setQuantity(null);
        $this->productStockEntity1->save();

        $isNeverOutOfStock = $this->stockFacade->isNeverOutOfStock(static::CONCRETE_SKU);

        $this->assertTrue($isNeverOutOfStock);
    }

    /**
     * @return void
     */
    public function testIsProductAbstractNeverOutOfStockForStoreShouldReturnTrueWhenOneOfTheConcreteProductsIsNeverOutOfStock(): void
    {
        //Arrange
        $this->productStockEntity1->setIsNeverOutOfStock(true);
        $this->productStockEntity1->save();

        //Act
        $isNeverOutOfStock = $this->stockFacade->isProductAbstractNeverOutOfStockForStore(
            static::ABSTRACT_SKU,
            $this->storeTransfer,
        );

        //Assert
        $this->assertTrue($isNeverOutOfStock);
    }

    /**
     * @return void
     */
    public function testIsProductAbstractNeverOutOfStockForStoreShouldReturnFalseWhenNoneOfTheConcreteProductsIsNeverOutOfStock(): void
    {
        //Arrange
        $this->productStockEntity1->setIsNeverOutOfStock(false);
        $this->productStockEntity1->save();

        //Act
        $isNeverOutOfStock = $this->stockFacade->isProductAbstractNeverOutOfStockForStore(
            static::ABSTRACT_SKU,
            $this->storeTransfer,
        );

        //Assert
        $this->assertFalse($isNeverOutOfStock);
    }

    /**
     * @return void
     */
    public function testCalculateStockForProductShouldCheckAllStocks(): void
    {
        $productStock = $this->stockFacade->calculateStockForProduct(static::CONCRETE_SKU);

        $this->assertTrue($productStock->equals('100.2'));
    }

    /**
     * @return void
     */
    public function testCalculateProductAbstractStockForStoreWillCalculateStockOfAllConcreteProductsOfAbstractProduct(): void
    {
        //Act
        $productAbstractStock = $this->stockFacade->calculateProductAbstractStockForStore(
            static::ABSTRACT_SKU,
            $this->storeTransfer,
        );

        //Assert
        $this->assertTrue($productAbstractStock->equals(static::STOCK_QUANTITY_1));
    }

    /**
     * @return void
     */
    public function testCreateStockType(): void
    {
        $stockTypeTransfer = (new TypeTransfer())
            ->setName('Test-Stock-Type');

        $idStock = $this->stockFacade->createStockType($stockTypeTransfer);

        $exists = SpyStockQuery::create()
            ->filterByIdStock($idStock)
            ->count() > 0;

        $this->assertTrue($exists);
    }

    /**
     * @return void
     */
    public function testCreateStockProduct(): void
    {
        $productAbstractEntity = new SpyProductAbstract();
        $productAbstractEntity
            ->setSku('foo')
            ->setAttributes('{}')
            ->save();

        $productConcreteEntity = new SpyProduct();
        $productConcreteEntity
            ->setSku('foo')
            ->setAttributes('{}')
            ->setFkProductAbstract($this->productAbstractEntity->getIdProductAbstract())
            ->save();

        $stockProductTransfer = (new StockProductTransfer())
            ->setStockType($this->stockEntity1->getName())
            ->setQuantity(static::STOCK_QUANTITY_1)
            ->setSku('foo');

        $idStockProduct = $this->stockFacade->createStockProduct($stockProductTransfer);

        $stockProductEntity = SpyStockProductQuery::create()
            ->filterByIdStockProduct($idStockProduct)
            ->findOne();

        $this->assertSame('92.0000000000', $stockProductEntity->getQuantity());
    }

    /**
     * @return void
     */
    public function testCreateStockProductShouldThrowException(): void
    {
        // Arrange
        $stockProductTransfer = (new StockProductTransfer())
            ->setStockType($this->stockEntity1->getName())
            ->setQuantity(static::STOCK_QUANTITY_1)
            ->setSku(static::CONCRETE_SKU);

        // Assert
        $this->expectException(StockProductAlreadyExistsException::class);
        $this->expectExceptionMessage('Cannot duplicate entry: this stock type is already set for this product');

        // Act
        $this->stockFacade->createStockProduct($stockProductTransfer);
    }

    /**
     * @return void
     */
    public function testUpdateStockProduct(): void
    {
        $stockProductTransfer = (new StockProductTransfer())
            ->setIdStockProduct($this->productStockEntity1->getIdStockProduct())
            ->setStockType($this->stockEntity1->getName())
            ->setQuantity(555)
            ->setSku(static::CONCRETE_SKU);

        $idStockProduct = $this->stockFacade->updateStockProduct($stockProductTransfer);

        $stockProductEntity = SpyStockProductQuery::create()
            ->filterByIdStockProduct($idStockProduct)
            ->findOne();

        $this->assertSame('555.0000000000', $stockProductEntity->getQuantity());
    }

    /**
     * @return void
     */
    public function testDecrementStockShouldReduceStockSize(): void
    {
        $this->stockFacade->decrementStockProduct(
            static::CONCRETE_SKU,
            $this->stockEntity1->getName(),
            new Decimal(10),
        );

        $stockSize = $this->stockFacade->calculateStockForProduct(static::CONCRETE_SKU);

        $this->assertTrue($stockSize->equals('90.2'));
    }

    /**
     * @return void
     */
    public function testIncrementStockShouldIncreaseStockSize(): void
    {
        $this->stockFacade->incrementStockProduct(
            static::CONCRETE_SKU,
            $this->stockEntity1->getName(),
            new Decimal(10),
        );

        $stockSize = $this->stockFacade->calculateStockForProduct(static::CONCRETE_SKU);

        $this->assertTrue($stockSize->equals('110.2'));
    }

    /**
     * @return void
     */
    public function testHasStockProductShouldReturnTrue(): void
    {
        $exists = $this->stockFacade->hasStockProduct(
            static::CONCRETE_SKU,
            $this->stockEntity1->getName(),
        );

        $this->assertTrue($exists);
    }

    /**
     * @return void
     */
    public function testHasStockProductShouldReturnFalse(): void
    {
        $exists = $this->stockFacade->hasStockProduct(
            'INVALIDSKU',
            'INVALIDTYPE',
        );

        $this->assertFalse($exists);
    }

    /**
     * @return void
     */
    public function testPersistStockProductCollection(): void
    {
        $increment = 20;

        $stockTransfer1 = (new StockProductTransfer())
            ->setSku(static::CONCRETE_SKU)
            ->setQuantity(static::STOCK_QUANTITY_1 + $increment)
            ->setIsNeverOutOfStock(false)
            ->setStockType($this->stockEntity1->getName());

        $stockTransfer2 = (new StockProductTransfer())
            ->setSku(static::CONCRETE_SKU)
            ->setQuantity(static::STOCK_QUANTITY_1 + $increment)
            ->setIsNeverOutOfStock(false)
            ->setStockType($this->stockEntity2->getName());

        $productConcreteTransfer = (new ProductConcreteTransfer())
            ->setStocks(new ArrayObject([
                $stockTransfer1, $stockTransfer2,
            ]));

        $this->stockFacade->persistStockProductCollection($productConcreteTransfer);

        $stockProductEntityCollection = SpyStockProductQuery::create()
            ->joinStock()
            ->filterByFkProduct($this->productConcreteEntity->getIdProduct())
            ->find();

        $this->assertNotEmpty($stockProductEntityCollection);

        foreach ($stockProductEntityCollection as $stockProductEntity) {
            // static::STOCK_QUANTITY_1 + $increment
            $this->assertSame('112.0000000000', $stockProductEntity->getQuantity());
            $this->assertSame($this->productConcreteEntity->getIdProduct(), $stockProductEntity->getFkProduct());
        }
    }

    /**
     * @return void
     */
    public function testExpandProductConcreteWithStocks(): void
    {
        $productConcreteTransfer = (new ProductConcreteTransfer())
            ->setIdProductConcrete($this->productConcreteEntity->getIdProduct())
            ->setSku(static::CONCRETE_SKU);

        $productConcreteTransfer = $this->stockFacade->expandProductConcreteWithStocks($productConcreteTransfer);

        $this->assertNotEmpty($productConcreteTransfer->getStocks());
        foreach ($productConcreteTransfer->getStocks() as $stock) {
            $this->assertTrue($stock->getQuantity()->greaterThan(0));
            $this->assertSame($stock->getSku(), static::CONCRETE_SKU);
        }
    }

    /**
     * @return void
     */
    public function testExpandProductConcreteTransfersWithStocksSuccessful(): void
    {
        // Arrange
        $productConcreteTransfer = (new ProductConcreteTransfer())
            ->setIdProductConcrete($this->productConcreteEntity->getIdProduct())
            ->setSku(static::CONCRETE_SKU);

        $productConcreteTransfer2 = (new ProductConcreteTransfer())
            ->setIdProductConcrete($this->productAbstractEntity->getIdProductAbstract())
            ->setSku('unknown');

        // Act
        $productConcreteTransfers = $this->stockFacade->expandProductConcreteTransfersWithStocks([$productConcreteTransfer, $productConcreteTransfer2]);

        // Assert
        $this->assertNotEmpty($productConcreteTransfers[0]->getStocks());
        foreach ($productConcreteTransfers[0]->getStocks() as $stock) {
            $this->assertTrue($stock->getQuantity()->greaterThan(0));
            $this->assertSame($stock->getSku(), static::CONCRETE_SKU);
        }

        $this->assertEmpty($productConcreteTransfers[1]->getStocks());
    }

    /**
     * @return void
     */
    public function testExpandProductConcreteWithStocksWillExpandOnlyWithActiveStocks(): void
    {
        //Arrange
        $this->stockEntity2->setIsActive(false)->save();
        $productConcreteTransfer = (new ProductConcreteTransfer())
            ->setIdProductConcrete($this->productConcreteEntity->getIdProduct())
            ->setSku(static::CONCRETE_SKU);

        //Act
        $productConcreteTransfer = $this->stockFacade->expandProductConcreteWithStocks($productConcreteTransfer);

        //Assert
        $this->assertNotEmpty($productConcreteTransfer->getStocks());
        foreach ($productConcreteTransfer->getStocks() as $stock) {
            $this->assertNotEquals($this->stockTransfer2->getIdStock(), $stock->getFkStock(), 'Stock ID did not match expected value.');
        }
    }

    /**
     * @return void
     */
    public function testGetAvailableStockTypesWillReturnCollectionOfStockNamesIndexedByStoreNames(): void
    {
        //Arrange
        $this->stockEntity2->setIsActive(false)->save();

        //Act
        $stocks = $this->stockFacade->getAvailableStockTypes();

        //Assert
        $this->assertArrayHasKey($this->stockTransfer1->getName(), $stocks, 'Available stock types collection does not match expected value.');
        $this->assertArrayHasKey($this->stockTransfer2->getName(), $stocks, 'Available stock types collection does not match expected value.');
        $this->assertContains($this->stockTransfer1->getName(), $stocks, 'Available stock types collection does not match expected value.');
        $this->assertContains($this->stockTransfer2->getName(), $stocks, 'Available stock types collection does not match expected value.');
    }

    /**
     * @return void
     */
    public function testGetStockProductsByIdProductWillReturnStockProductWhereStockIsActive(): void
    {
        //Arrange
        $this->stockEntity2->setIsActive(false)->save();

        //Act
        $stockProductTransfers = $this->stockFacade->getStockProductsByIdProduct($this->productConcreteEntity->getIdProduct());

        //Assert
        $this->assertCount(1, $stockProductTransfers, 'Stock products count does not match expected value.');
        $this->assertTrue(
            $stockProductTransfers[0]->getQuantity()->equals(static::STOCK_QUANTITY_1),
            'Stock product quantity does not match expected value.',
        );
        $this->assertSame(
            $this->stockTransfer1->getIdStock(),
            $stockProductTransfers[0]->getFkStock(),
            'Stock ID does not match expected value.',
        );
    }

    /**
     * @return void
     */
    public function testGetStockTypesForStoreWillReturnCollectionOfStockNamesIndexedByStockName(): void
    {
        //Act
        $stockCollection = $this->stockFacade->getStockTypesForStore($this->storeTransfer);

        //Assert
        $this->assertIsArray($stockCollection, 'Stock types collection should be an array.');
        $this->assertArrayHasKey($this->stockTransfer1->getName(), $stockCollection, 'Stock types collection does not match expected value.');
        $this->assertContains($this->stockTransfer1->getName(), $stockCollection, 'Stock types collection does not match expected value.');
    }

    /**
     * @return void
     */
    public function testGetWarehouseToStoreMappingWillReturnCollectionOfStocksWithCollectionOfStoresNamesIndexedByStoresName(): void
    {
        //Arrange
        $this->tester->haveStockStoreRelation(
            (new StockTransfer())->fromArray($this->stockEntity2->toArray(), true),
            $this->storeTransfer,
        );

        //Act
        $stockCollection = $this->stockFacade->getWarehouseToStoreMapping();

        //Assert
        $this->assertIsArray($stockCollection, 'Warehouse to store mapping collection should be an array.');
        $storeName = $this->storeTransfer->getName();
        $this->assertArrayHasKey($this->stockTransfer1->getName(), $stockCollection, 'Warehouse to store mapping collection does not match expected value.');
        $this->assertArrayHasKey($this->stockTransfer2->getName(), $stockCollection, 'Warehouse to store mapping collection does not match expected value.');
        $this->assertEquals([
            $storeName => $storeName,
        ], $stockCollection[$this->stockTransfer1->getName()], 'Warehouse to store mapping collection does not match expected value.');
        $this->assertEquals([
            $storeName => $storeName,
        ], $stockCollection[$this->stockTransfer2->getName()], 'Warehouse to store mapping collection does not match expected value.');
    }

    /**
     * @return void
     */
    public function testGetStoreToWarehouseMappingWillReturnCollectionOfStoreNamesWithCollectionOfStockNamesIndexedByStockName(): void
    {
        //Arrange
        /** @var \Generated\Shared\Transfer\StoreTransfer $storeTransfer2 */
        $storeTransfer2 = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_AT]);
        $this->assignStockToStore($storeTransfer2, $this->stockTransfer1);
        $this->assignStockToStore($storeTransfer2, $this->stockTransfer2);

        //Act
        $storeToWarehouseMapping = $this->stockFacade->getStoreToWarehouseMapping();

        //Assert
        $this->assertArrayHasKey(
            $this->storeTransfer->getName(),
            $storeToWarehouseMapping,
            'Store to warehouse mapping collection does not have expected key.',
        );
        $this->assertContains(
            $this->stockTransfer1->getName(),
            $storeToWarehouseMapping[$this->storeTransfer->getName()],
            'Store to warehouse mapping collection does not match expected value.',
        );

        $this->assertArrayHasKey(
            $storeTransfer2->getName(),
            $storeToWarehouseMapping,
            'Store to warehouse mapping collection does not have expected key.',
        );
        $this->assertContains(
            $this->stockTransfer1->getName(),
            $storeToWarehouseMapping[$storeTransfer2->getName()],
            'Store to warehouse mapping collection does not match expected value.',
        );
        $this->assertContains(
            $this->stockTransfer2->getName(),
            $storeToWarehouseMapping[$storeTransfer2->getName()],
            'Store to warehouse mapping collection does not match expected value.',
        );
    }

    /**
     * @return void
     */
    public function testFindStockProductsByIdProductForStoreWillReturnCollectionOfStockProducts(): void
    {
        //Arrange
        $this->tester->haveStockStoreRelation($this->stockTransfer2, $this->storeTransfer);

        //Act
        $productStockCollection = $this->stockFacade->findStockProductsByIdProductForStore(
            $this->productConcreteEntity->getIdProduct(),
            $this->storeTransfer,
        );

        //Assert
        $this->assertIsArray($productStockCollection, 'Product stock collection should be an array.');
        $this->assertCount(2, $productStockCollection, 'Product stock collection count does not match expected value.');
        foreach ($productStockCollection as $stockProductTransfer) {
            $this->assertSame(
                $this->productConcreteEntity->getSku(),
                $stockProductTransfer->getSku(),
                'Concrete product sku of stock product does not match expected value.',
            );
        }
    }

    /**
     * @return void
     */
    public function testFindStockProductsByIdProductForStoreWillReturnCollectionOfStockProductsWithInactiveStocksIncluded(): void
    {
        //Arrange
        $this->tester->haveStockStoreRelation($this->stockTransfer2, $this->storeTransfer);
        $this->stockEntity2->setIsActive(false)->save();

        //Act
        $productStockCollection = $this->stockFacade->findStockProductsByIdProductForStore(
            $this->productConcreteEntity->getIdProduct(),
            $this->storeTransfer,
        );

        //Assert
        $this->assertIsArray($productStockCollection, 'Product stock collection should be an array.');
        $this->assertCount(2, $productStockCollection, 'Product stock collection count does not match expected value.');
        foreach ($productStockCollection as $stockProductTransfer) {
            $this->assertSame(
                $this->productConcreteEntity->getSku(),
                $stockProductTransfer->getSku(),
                'Concrete product sku of stock product does not match expected value.',
            );
        }
    }

    /**
     * @return void
     */
    public function testGetStoresWhereProductStockIsDefinedWillReturnArrayOfStoreTransfers(): void
    {
        //Act
        $storeTransfers = $this->stockFacade->getStoresWhereProductStockIsDefined(static::CONCRETE_SKU);

        //Assert
        $this->assertCount(1, $storeTransfers, 'Number of store does not match expected value.');
        $this->assertInstanceOf(
            StoreTransfer::class,
            $storeTransfers[0],
            sprintf('Store transfer should be an instance of %s.', StoreTransfer::class),
        );
        $this->assertSame(
            $this->storeTransfer->getIdStore(),
            $storeTransfers[0]->getIdStore(),
            'Store ID does not match expected value.',
        );
        $this->assertSame(
            $this->storeTransfer->getName(),
            $storeTransfers[0]->getName(),
            'Store name does not match expected value.',
        );
    }

    /**
     * @return void
     */
    public function testFindStockByNameWillFindExistingStock(): void
    {
        //Act
        $stockTransfer = $this->stockFacade->findStockByName($this->stockTransfer1->getName());

        //Assert
        $this->assertSame($stockTransfer->getIdStock(), $this->stockTransfer1->getIdStock(), 'Stock ID does not match expected value.');
        $this->assertSame($stockTransfer->getName(), $this->stockTransfer1->getName(), 'Stock name does not match expected value.');
    }

    /**
     * @return void
     */
    public function testFindStockByNameWillReturnNullForNonExistedStockName(): void
    {
        //Act
        $result = $this->stockFacade->findStockByName('Non-existing stock name');

        //Assert
        $this->assertNull($result, 'Result should be null.');
    }

    /**
     * @return void
     */
    public function testCreateStockWillCreateStock(): void
    {
        //Arrange
        $originStockTransfer = ((new StockBuilder())->build())
            ->setIsActive(false);

        //Act
        $stockResponseTransfer = $this->stockFacade->createStock($originStockTransfer);

        //Assert
        $this->assertTrue($stockResponseTransfer->getIsSuccessful(), 'Stock response should be successful.');
        $stockTransfer = $stockResponseTransfer->getStock();
        $this->assertIsInt($stockTransfer->getIdStock(), 'Stock ID should be integer value.');
        $this->assertSame($originStockTransfer->getName(), $stockTransfer->getName(), 'Stock name does not match expected value.');
        $this->assertSame($originStockTransfer->getIsActive(), $stockTransfer->getIsActive(), 'Stock active status does not match expected value.');
    }

    /**
     * @return void
     */
    public function testCreateStockWithRelationToStoreWillCreateStockWithRelations(): void
    {
        //Arrange
        $storeRelationTransfer = (new StoreRelationTransfer())
            ->setIdStores([$this->storeTransfer->getIdStore()]);
        $originStockTransfer = ((new StockBuilder())->build())
            ->setIsActive(false)
            ->setStoreRelation($storeRelationTransfer);

        //Act
        $stockResponseTransfer = $this->stockFacade->createStock($originStockTransfer);
        $storeStockRelation = $this->stockFacade->getStockTypesForStore($this->storeTransfer);

        //Assert
        $this->assertTrue($stockResponseTransfer->getIsSuccessful(), 'Stock response should be successful.');
        $stockTransfer = $stockResponseTransfer->getStock();
        $this->assertIsInt($stockTransfer->getIdStock(), 'Stock ID should be integer value.');
        $this->assertSame($originStockTransfer->getName(), $stockTransfer->getName(), 'Stock name does not match expected value.');
        $this->assertSame($originStockTransfer->getIsActive(), $stockTransfer->getIsActive(), 'Stock active status does not match expected value.');
        $this->assertNotNull($stockTransfer->getStoreRelation(), 'Stock should have store relations.');
        $this->assertEquals(
            $storeRelationTransfer->getIdStores(),
            $stockTransfer->getStoreRelation()->getIdStores(),
            'IDs of related stores does not match expected value.',
        );
        $this->assertContains($stockTransfer->getName(), $storeStockRelation, 'Store relation does not contain expected store name.');
    }

    /**
     * @return void
     */
    public function testFindStockByIdShouldReturnStockTransferForExistingStockId(): void
    {
        //Act
        $stockTransfer = $this->stockFacade->findStockById($this->stockTransfer1->getIdStock());

        //Assert
        $this->assertSame($this->stockTransfer1->getIdStock(), $stockTransfer->getIdStock(), 'Stock ID should be integer value.');
        $this->assertSame($this->stockTransfer1->getName(), $stockTransfer->getName(), 'Stock name does not match expected value.');
        $this->assertSame($this->stockTransfer1->getIsActive(), $stockTransfer->getIsActive(), 'Stock active status does not match expected value.');
    }

    /**
     * @return void
     */
    public function testFindStockByIdShouldReturnNullForNonExistingStockId(): void
    {
        //Act
        $result = $this->stockFacade->findStockById(-1);

        //Assert
        $this->assertNull($result, 'The result must be null');
    }

    /**
     * @return void
     */
    public function testUpdateStockShouldUpdateStockName(): void
    {
        //Arrange
        $newStockName = 'new name';
        $originStockTransfer = $this->tester->haveStock();
        $originStockTransfer->setName($newStockName);

        //Act
        $stockResponseTransfer = $this->stockFacade->updateStock($originStockTransfer);

        //Assert
        $this->assertTrue($stockResponseTransfer->getIsSuccessful(), 'Stock response should be successful.');
        $stockTransfer = $stockResponseTransfer->getStock();
        $this->assertSame($newStockName, $stockTransfer->getName(), 'Stock name does not match expected value.');
    }

    /**
     * @return void
     */
    public function testUpdateStockShouldUpdateStockStatus(): void
    {
        //Arrange
        $originStockTransfer = $this->stockTransfer1;
        $originStockTransfer->setIsActive(false);

        //Act
        $stockResponseTransfer = $this->stockFacade->updateStock($originStockTransfer);

        //Assert
        $this->assertTrue($stockResponseTransfer->getIsSuccessful(), 'Stock response should be successful.');
        $stockTransfer = $stockResponseTransfer->getStock();
        $this->assertSame($originStockTransfer->getIsActive(), $stockTransfer->getIsActive(), 'Stock active status does not match expected value.');
    }

    /**
     * @return void
     */
    public function testUpdateStockShouldAddStoreRelations(): void
    {
        //Arrange
        $storeRelationTransfer = (new StoreRelationTransfer())
            ->setIdStores([$this->storeTransfer->getIdStore()]);
        $originStockTransfer = $this->stockTransfer2;
        $originStockTransfer->setStoreRelation($storeRelationTransfer);

        //Act
        $stockResponseTransfer = $this->stockFacade->updateStock($originStockTransfer);
        $storeStockRelation = $this->stockFacade->getStockTypesForStore($this->storeTransfer);

        //Assert
        $this->assertTrue($stockResponseTransfer->getIsSuccessful(), 'Stock response should be successful.');
        $stockTransfer = $stockResponseTransfer->getStock();
        $this->assertContains($stockTransfer->getName(), $storeStockRelation, 'Store relation does not contain expected store name.');
    }

    /**
     * @return void
     */
    public function testUpdateStockShouldRemoveStoreRelations(): void
    {
        //Arrange
        $storeRelationTransfer = (new StoreRelationTransfer())->setIdStores([]);
        $originStockTransfer = $this->stockTransfer1;
        $originStockTransfer->setStoreRelation($storeRelationTransfer);

        //Act
        $stockResponseTransfer = $this->stockFacade->updateStock($originStockTransfer);
        $storeStockRelation = $this->stockFacade->getStockTypesForStore($this->storeTransfer);

        //Assert
        $this->assertTrue($stockResponseTransfer->getIsSuccessful(), 'Stock response should be successful.');
        $stockTransfer = $stockResponseTransfer->getStock();
        $this->assertNotContains($stockTransfer->getName(), $storeStockRelation, 'Store relation should not contain store name.');
    }

    /**
     * @return void
     */
    public function testGetStocksByStockCriteriaFilterWillFilterStocksByName(): void
    {
        // Arrange
        $stockCriteriaFilterTransfer = (new StockCriteriaFilterTransfer())
            ->setIdStock($this->stockEntity1->getIdStock())
            ->addStockName($this->stockEntity1->getName());

        // Act
        $stockCollectionTransfer = $this->stockFacade->getStocksByStockCriteriaFilter($stockCriteriaFilterTransfer);

        // Assert
        $this->assertCount(1, $stockCollectionTransfer->getStocks(), 'Stocks count does not match expected value.');
        $this->assertSame(
            $this->stockEntity1->getName(),
            $stockCollectionTransfer->getStocks()->offsetGet(0)->getNameOrFail(),
            'Stock name does not match expected value.',
        );
    }

    /**
     * @return void
     */
    public function testGetStocksByStockCriteriaFilterWillReturnEmptyCollectionForNonExistingStockName(): void
    {
        // Arrange
        $stockCriteriaFilterTransfer = (new StockCriteriaFilterTransfer())->addStockName('SOME_RANDOM_STOCK_NAME');

        // Act
        $stockCollectionTransfer = $this->stockFacade->getStocksByStockCriteriaFilter($stockCriteriaFilterTransfer);

        // Assert
        $this->assertCount(0, $stockCollectionTransfer->getStocks(), 'Stocks count does not match expected value.');
    }

    /**
     * @return void
     */
    public function testGetStocksByStockCriteriaFilterWillReturnEmptyCollectionForNonExistingStockId(): void
    {
        // Arrange
        $stockCriteriaFilterTransfer = (new StockCriteriaFilterTransfer())->setIdStock(static::INVALID_STOCK_ID);

        // Act
        $stockCollectionTransfer = $this->stockFacade->getStocksByStockCriteriaFilter($stockCriteriaFilterTransfer);

        // Assert
        $this->assertCount(0, $stockCollectionTransfer->getStocks(), 'Stocks count does not match expected value.');
    }

    /**
     * @return void
     */
    public function testGetStocksByStockCriteriaFilterWillFilterStocksByActiveStatus(): void
    {
        // Arrange
        $stockCriteriaFilterTransfer = (new StockCriteriaFilterTransfer())->setIsActive(true);

        // Act
        $stockCollectionTransfer = $this->stockFacade->getStocksByStockCriteriaFilter($stockCriteriaFilterTransfer);

        // Assert
        $this->assertGreaterThanOrEqual(2, $stockCollectionTransfer->getStocks()->count(), 'Stocks count does not match expected value.');

        $resultStockNames = array_map(function (StockTransfer $stockTransfer) {
            return $stockTransfer->getNameOrFail();
        }, $stockCollectionTransfer->getStocks()->getArrayCopy());
        $this->assertTrue(in_array($this->stockEntity1->getName(), $resultStockNames, true), 'Expected stock name is missing in returned stock collection.');
        $this->assertTrue(in_array($this->stockEntity2->getName(), $resultStockNames, true), 'Expected stock name is missing in returned stock collection.');
    }

    /**
     * @return void
     */
    public function testGetStocksByStockCriteriaFilterWillFilterStocksByStoreName(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->tester->haveStockStoreRelation($this->stockTransfer1, $storeTransfer);

        $stockCriteriaFilterTransfer = (new StockCriteriaFilterTransfer())->addStoreName(static::STORE_NAME_DE);

        // Act
        $stockCollectionTransfer = $this->stockFacade->getStocksByStockCriteriaFilter($stockCriteriaFilterTransfer);
        $availableStocksForStore = $this->stockFacade->getAvailableWarehousesForStore($storeTransfer);

        // Assert
        $this->assertCount(count($availableStocksForStore), $stockCollectionTransfer->getStocks(), 'Stocks count does not match expected value.');
    }

    /**
     * @return void
     */
    protected function setupData(): void
    {
        $this->storeTransfer = $this->tester->haveStore([
            StoreTransfer::NAME => static::STORE_NAME_DE,
        ]);

        $this->productAbstractEntity = new SpyProductAbstract();
        $this->productAbstractEntity
            ->setSku(static::ABSTRACT_SKU)
            ->setAttributes('{}')
            ->save();

        $this->productConcreteEntity = new SpyProduct();
        $this->productConcreteEntity
            ->setSku(static::CONCRETE_SKU)
            ->setAttributes('{}')
            ->setFkProductAbstract($this->productAbstractEntity->getIdProductAbstract())
            ->save();

        $this->stockEntity1 = new SpyStock();
        $this->stockEntity1
            ->setName('TEST')
            ->save();
        $this->stockTransfer1 = $this->mapStockEntityToStockTransfer($this->stockEntity1, new StockTransfer());
        $this->assignStockToStore($this->storeTransfer, $this->stockTransfer1);

        $this->productStockEntity1 = new SpyStockProduct();
        $this->productStockEntity1
            ->setFkStock($this->stockEntity1->getIdStock())
            ->setQuantity(static::STOCK_QUANTITY_1)
            ->setIsNeverOutOfStock(false)
            ->setFkProduct($this->productConcreteEntity->getIdProduct())
            ->save();

        $this->stockEntity2 = new SpyStock();
        $this->stockEntity2
            ->setName('TEST2')
            ->save();
        $this->stockTransfer2 = $this->mapStockEntityToStockTransfer($this->stockEntity2, new StockTransfer());

        $this->productStockEntity2 = new SpyStockProduct();
        $this->productStockEntity2
            ->setFkStock($this->stockEntity2->getIdStock())
            ->setQuantity(static::STOCK_QUANTITY_2)
            ->setIsNeverOutOfStock(false)
            ->setFkProduct($this->productConcreteEntity->getIdProduct())
            ->save();
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param \Generated\Shared\Transfer\StockTransfer $stockTransfer
     *
     * @return void
     */
    protected function assignStockToStore(StoreTransfer $storeTransfer, StockTransfer $stockTransfer): void
    {
        $this->tester->haveStockStoreRelation(
            $stockTransfer,
            $storeTransfer,
        );
    }

    /**
     * @param \Orm\Zed\Stock\Persistence\SpyStock $stockEntity
     * @param \Generated\Shared\Transfer\StockTransfer $stockTransfer
     *
     * @return \Generated\Shared\Transfer\StockTransfer
     */
    protected function mapStockEntityToStockTransfer(SpyStock $stockEntity, StockTransfer $stockTransfer): StockTransfer
    {
        return $stockTransfer->fromArray($stockEntity->toArray(), true);
    }
}

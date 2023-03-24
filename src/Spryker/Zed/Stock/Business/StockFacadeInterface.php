<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business;

use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StockCollectionTransfer;
use Generated\Shared\Transfer\StockCriteriaFilterTransfer;
use Generated\Shared\Transfer\StockCriteriaTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\StockResponseTransfer;
use Generated\Shared\Transfer\StockTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\TypeTransfer;
use Spryker\DecimalObject\Decimal;

interface StockFacadeInterface
{
    /**
     * Specification:
     * - Checks if the concrete product with the provided SKU has any stock type that is set as "never out of stock".
     * - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param string $sku
     *
     * @return bool
     */
    public function isNeverOutOfStock($sku);

    /**
     * Specification:
     * - Checks if the concrete product with the provided SKU has any stock type that is set as "never out of stock".
     * - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return bool
     */
    public function isNeverOutOfStockForStore($sku, StoreTransfer $storeTransfer);

    /**
     * Specification:
     * - Checks if the abstract product with the provided SKU has any stock product that is set as "never out of stock".
     * - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return bool
     */
    public function isProductAbstractNeverOutOfStockForStore(string $abstractSku, StoreTransfer $storeTransfer): bool;

    /**
     * Specification:
     *  - Returns the total stock amount of the concrete product for all its available stock types.
     *  - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param string $sku
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    public function calculateStockForProduct(string $sku): Decimal;

    /**
     * Specification:
     *  - Returns the total stock amount of the concrete product for all its available stock types and store.
     *  - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    public function calculateProductStockForStore(string $sku, StoreTransfer $storeTransfer): Decimal;

    /**
     * Specification:
     *  - Returns the total stock amount of the abstract product's concrete products for all theirs available stocks and store.
     *  - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    public function calculateProductAbstractStockForStore(string $abstractSku, StoreTransfer $storeTransfer): Decimal;

    /**
     * Specification:
     * - Persists a new stock type entity to database.
     * - Touches the newly created stock type.
     * - Returns the ID of the new stock type entity.
     *
     * @api
     *
     * @deprecated Use {@link createStock()} instead.
     *
     * @param \Generated\Shared\Transfer\TypeTransfer $stockTypeTransfer
     *
     * @return int
     */
    public function createStockType(TypeTransfer $stockTypeTransfer);

    /**
     * Specification:
     * - Persists a new stock product entity in database for the given product and stock type.
     * - If the product already have stock assigned in the given stock type, then it throws an exception.
     * - Touches the newly created stock product.
     * - Returns the ID of the new stock product entity.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     *
     * @return int
     */
    public function createStockProduct(StockProductTransfer $transferStockProduct);

    /**
     * Specification:
     * - Updates an existing stock product entity with the provided stock data.
     * - Touches the stock product.
     * - Returns the ID of the stock product entity.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StockProductTransfer $stockProductTransfer
     *
     * @return int
     */
    public function updateStockProduct(StockProductTransfer $stockProductTransfer);

    /**
     * Specification:
     * - Decrements stock amount of the given concrete product for the given stock type.
     * - Touches the stock product.
     *
     * @api
     *
     * @param string $sku
     * @param string $stockType
     * @param \Spryker\DecimalObject\Decimal $decrementBy
     *
     * @return void
     */
    public function decrementStockProduct($sku, $stockType, Decimal $decrementBy): void;

    /**
     * Specification:
     * - Increments stock amount of the given concrete product for the given stock type.
     * - Touches the stock product.
     *
     * @api
     *
     * @param string $sku
     * @param string $stockType
     * @param \Spryker\DecimalObject\Decimal $incrementBy
     *
     * @return void
     */
    public function incrementStockProduct($sku, $stockType, Decimal $incrementBy): void;

    /**
     * Specification:
     * - Checks if the given concrete product for the given stock type has positive amount.
     *
     * @api
     *
     * @param string $sku
     * @param string $stockType
     *
     * @return bool
     */
    public function hasStockProduct($sku, $stockType);

    /**
     * Specification:
     * - Processes all provided stocks of the concrete product transfer.
     * - If a stock entry from the collection doesn't exists for the product, then it will be newly created.
     * - If a stock entry from the collection exists for the product, then it will be updated with the provided data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function persistStockProductCollection(ProductConcreteTransfer $productConcreteTransfer);

    /**
     * Specification:
     *  - Expands concrete product transfer (by the ID of the product) with it's stock information from the database.
     *  - Filters out stocks that are inactive.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\Stock\Business\StockFacadeInterface::expandProductConcreteTransfersWithStocks()} instead.
     *
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function expandProductConcreteWithStocks(ProductConcreteTransfer $productConcreteTransfer);

    /**
     * Specification:
     *  - Expands transfers of product concrete with stock information from the database.
     *  - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteTransfer>
     */
    public function expandProductConcreteTransfersWithStocks(array $productConcreteTransfers): array;

    /**
     * Specification:
     *  - Returns all available stock types.
     *
     * @api
     *
     * @return array<string>
     */
    public function getAvailableStockTypes();

    /**
     * Specification:
     *  - Returns stock product by given id product.
     *  - Filters out stocks that are inactive.
     *
     * @api
     *
     * @param int $idProductConcrete
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function getStockProductsByIdProduct($idProductConcrete);

    /**
     * Specification:
     *  - Returns stock product by given id product.
     *
     * @api
     *
     * @param int $idProductConcrete
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function findStockProductsByIdProductForStore($idProductConcrete, StoreTransfer $storeTransfer);

    /**
     * Specification:
     *  - Gets stock types for store.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<string>
     */
    public function getStockTypesForStore(StoreTransfer $storeTransfer);

    /**
     * Specification:
     *  - Returns stock mapping per store/warehouse pair:
     *
     *  [
     *    'Warehouse1' => ['DE', 'US'],
     *    'Warehouse1' => ['US']
     * ]
     *
     * @api
     *
     * @return array<array<string>>
     */
    public function getWarehouseToStoreMapping();

    /**
     * Specification:
     *  - Returns stock configuration mock per store/warehouse pair:
     *
     * [
     *     'DE' => ['Warehouse1']
     *     'US' => [ 'Warehouse1', 'Warehouse2'],
     * ]
     *
     * @api
     *
     * @return array<array<string>>
     */
    public function getStoreToWarehouseMapping();

    /**
     * Specification:
     *  - Finds stock by given id.
     *  - Returns StockTransfer or null if there are no records in database.
     *
     * @api
     *
     * @param int $idStock
     *
     * @return \Generated\Shared\Transfer\StockTransfer|null
     */
    public function findStockById(int $idStock): ?StockTransfer;

    /**
     * Specification:
     *  - Finds stock by given stock name.
     *  - Returns StockTransfer or null if there are no records in database.
     *
     * @api
     *
     * @param string $stockName
     *
     * @return \Generated\Shared\Transfer\StockTransfer|null
     */
    public function findStockByName(string $stockName): ?StockTransfer;

    /**
     * Specification:
     *  - Persists a new stock entity to database.
     *  - Touches the newly created stock.
     *  - Executes {@link \Spryker\Zed\StockExtension\Dependency\Plugin\StockPostCreatePluginInterface} plugin stack.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StockTransfer $stockTransfer
     *
     * @return \Generated\Shared\Transfer\StockResponseTransfer
     */
    public function createStock(StockTransfer $stockTransfer): StockResponseTransfer;

    /**
     * Specification:
     *  - Updates stock.
     *  - Updates stock store relationships.
     *  - Persists stock entity to database.
     *  - Touches the newly created stock.
     *  - Executes {@link \Spryker\Zed\StockExtension\Dependency\Plugin\StockPostUpdatePluginInterface} plugin stack.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StockTransfer $stockTransfer
     *
     * @return \Generated\Shared\Transfer\StockResponseTransfer
     */
    public function updateStock(StockTransfer $stockTransfer): StockResponseTransfer;

    /**
     * Specification:
     *  - Returns all stores that have relationship with stock where product with given sku is defined.
     *
     * @api
     *
     * @param string $sku
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getStoresWhereProductStockIsDefined(string $sku): array;

    /**
     * Specification:
     *  - Returns all available stock types for given store.
     *  - Filters out stocks that are inactive.
     *  - StoreTransfer.name is required.
     *  - Executes {@link \Spryker\Zed\StockExtension\Dependency\Plugin\StockCollectionExpanderPluginInterface} plugin stack.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\StockTransfer>
     */
    public function getAvailableWarehousesForStore(StoreTransfer $storeTransfer): array;

    /**
     * Specification:
     * - Gets Stock collection by criteria filter.
     * - Executes {@link \Spryker\Zed\StockExtension\Dependency\Plugin\StockCollectionExpanderPluginInterface} plugin stack.
     *
     * @api
     *
     * @deprecated Use {@link getStockCollection()} instead.
     *
     * @param \Generated\Shared\Transfer\StockCriteriaFilterTransfer $stockCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\StockCollectionTransfer
     */
    public function getStocksByStockCriteriaFilter(StockCriteriaFilterTransfer $stockCriteriaFilterTransfer): StockCollectionTransfer;

    /**
     * Specification:
     * - Gets Stock collection by criteria.
     * - Uses `StockCriteriaTransfer.StockConditions.stockIds` to filter stocks by stockIds.
     * - Uses `StockCriteriaTransfer.StockConditions.uuids` to filter stocks by uuids.
     * - Uses `StockCriteriaTransfer.StockConditions.isActive` to filter active stocks.
     * - Uses `StockCriteriaTransfer.StockConditions.storeNames` to filter stocks by store names.
     * - Uses `StockCriteriaTransfer.StockConditions.stockNames` to filter stocks by stock names.
     * - Executes the stack of {@link \Spryker\Zed\StockExtension\Dependency\Plugin\StockCollectionExpanderPluginInterface} plugins.
     * - Returns `StockCollectionTransfer` filled with found stocks.
     *
     * @api
     *
     * {@internal filter by uuids works if `StockTransfer.uuid` field is provided by another module.}
     *
     * @param \Generated\Shared\Transfer\StockCriteriaTransfer $stockCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\StockCollectionTransfer
     */
    public function getStockCollection(StockCriteriaTransfer $stockCriteriaTransfer): StockCollectionTransfer;
}

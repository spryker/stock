<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\StockProduct;

use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\Stock\Persistence\SpyStockProduct;

interface StockProductReaderInterface
{
    /**
     * @param string $sku
     *
     * @return bool
     */
    public function isNeverOutOfStock(string $sku): bool;

    /**
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return bool
     */
    public function isNeverOutOfStockForStore(string $sku, StoreTransfer $storeTransfer): bool;

    /**
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return bool
     */
    public function isProductAbstractNeverOutOfStockForStore(string $abstractSku, StoreTransfer $storeTransfer): bool;

    /**
     * @param string $sku
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function getStocksProduct(string $sku): array;

    /**
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function findProductStocksForStore(string $sku, StoreTransfer $storeTransfer): array;

    /**
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function getStockProductByProductAbstractSkuForStore(string $abstractSku, StoreTransfer $storeTransfer): array;

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return bool
     */
    public function hasStockProduct(string $sku, string $stockType): bool;

    /**
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return bool
     */
    public function hastStockProductInStore(string $sku, StoreTransfer $storeTransfer): bool;

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return int
     */
    public function getIdStockProduct(string $sku, string $stockType): int;

    /**
     * @param string $sku
     *
     * @throws \Spryker\Zed\Product\Business\Exception\MissingProductException
     *
     * @return int|null
     */
    public function findProductAbstractIdBySku(string $sku): ?int;

    /**
     * @param string $sku
     *
     * @throws \Spryker\Zed\Product\Business\Exception\MissingProductException
     *
     * @return int
     */
    public function getProductConcreteIdBySku(string $sku): int;

    /**
     * @param int $idStockProduct
     *
     * @throws \Spryker\Zed\Stock\Business\Exception\StockProductNotFoundException
     *
     * @return \Orm\Zed\Stock\Persistence\SpyStockProduct
     */
    public function getStockProductById($idStockProduct): SpyStockProduct;

    /**
     * @param int $idProductConcrete
     *
     * @throws \Spryker\Zed\Stock\Business\Exception\StockProductNotFoundException
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function getStockProductsByIdProduct($idProductConcrete): array;

    /**
     * @param int $idProductConcrete
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\StockProductTransfer>
     */
    public function findStockProductsByIdProductForStore($idProductConcrete, StoreTransfer $storeTransfer): array;

    /**
     * @deprecated Use {@link \Spryker\Zed\Stock\Business\StockProduct\StockProductReaderInterface::expandProductConcreteTransfersWithStocks()} instead.
     *
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function expandProductConcreteWithStocks(ProductConcreteTransfer $productConcreteTransfer): ProductConcreteTransfer;

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteTransfer>
     */
    public function expandProductConcreteTransfersWithStocks(array $productConcreteTransfers): array;

    /**
     * @param int $idStockType
     * @param int $idProduct
     *
     * @throws \Spryker\Zed\Stock\Business\Exception\StockProductAlreadyExistsException
     *
     * @return void
     */
    public function checkStockDoesNotExist($idStockType, $idProduct): void;
}

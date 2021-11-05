<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\Model;

use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\TypeTransfer;
use Orm\Zed\Stock\Persistence\SpyStock;
use Orm\Zed\Stock\Persistence\SpyStockProduct;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use Spryker\DecimalObject\Decimal;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\Stock\Business\Stock\StockReaderInterface;
use Spryker\Zed\Stock\Business\StockProduct\StockProductReaderInterface;
use Spryker\Zed\Stock\Dependency\Facade\StockToTouchInterface;
use Spryker\Zed\Stock\Persistence\StockQueryContainerInterface;

class Writer implements WriterInterface
{
    use TransactionTrait;

    /**
     * @var string
     */
    public const TOUCH_STOCK_TYPE = 'stock-type';

    /**
     * @var string
     */
    public const TOUCH_STOCK_PRODUCT = 'stock-product';

    /**
     * @var string
     */
    public const ERROR_STOCK_TYPE_UNKNOWN = 'stock type unknown';

    /**
     * @var \Spryker\Zed\Stock\Persistence\StockQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\Stock\Business\Stock\StockReaderInterface
     */
    protected $stockReader;

    /**
     * @var \Spryker\Zed\Stock\Business\StockProduct\StockProductReaderInterface
     */
    protected $stockProductReader;

    /**
     * @var \Spryker\Zed\Stock\Dependency\Facade\StockToTouchInterface
     */
    protected $touchFacade;

    /**
     * @var array<\Spryker\Zed\StockExtension\Dependency\Plugin\StockUpdateHandlerPluginInterface>
     */
    protected $stockUpdateHandlerPlugins;

    /**
     * @param \Spryker\Zed\Stock\Persistence\StockQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Stock\Business\Stock\StockReaderInterface $stockReader
     * @param \Spryker\Zed\Stock\Business\StockProduct\StockProductReaderInterface $stockProductReader
     * @param \Spryker\Zed\Stock\Dependency\Facade\StockToTouchInterface $touchFacade
     * @param array<\Spryker\Zed\StockExtension\Dependency\Plugin\StockUpdateHandlerPluginInterface> $stockUpdateHandlerPlugins
     */
    public function __construct(
        StockQueryContainerInterface $queryContainer,
        StockReaderInterface $stockReader,
        StockProductReaderInterface $stockProductReader,
        StockToTouchInterface $touchFacade,
        array $stockUpdateHandlerPlugins
    ) {
        $this->queryContainer = $queryContainer;
        $this->stockReader = $stockReader;
        $this->stockProductReader = $stockProductReader;
        $this->touchFacade = $touchFacade;
        $this->stockUpdateHandlerPlugins = $stockUpdateHandlerPlugins;
    }

    /**
     * @deprecated Use {@link \Spryker\Zed\Stock\Business\Stock\StockCreator::createStock()} instead.
     *
     * @param \Generated\Shared\Transfer\TypeTransfer $stockTypeTransfer
     *
     * @return int
     */
    public function createStockType(TypeTransfer $stockTypeTransfer)
    {
        $idStock = $this->getTransactionHandler()->handleTransaction(function () use ($stockTypeTransfer) {
            return $this->executeCreateStockTypeTransaction($stockTypeTransfer);
        });

        return $idStock;
    }

    /**
     * @param \Generated\Shared\Transfer\TypeTransfer $stockTypeTransfer
     *
     * @return int
     */
    protected function executeCreateStockTypeTransaction(TypeTransfer $stockTypeTransfer)
    {
        $stockEntity = (new SpyStockQuery())
            ->filterByName($stockTypeTransfer->getName())
            ->findOneOrCreate();

        $stockEntity->save();

        $this->insertActiveTouchRecordStockType($stockEntity);

        return $stockEntity->getPrimaryKey();
    }

    /**
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     *
     * @return int
     */
    public function createStockProduct(StockProductTransfer $transferStockProduct)
    {
        $idStockProduct = $this->getTransactionHandler()->handleTransaction(function () use ($transferStockProduct) {
            return $this->executeCreateStockProductTransaction($transferStockProduct);
        });

        return $idStockProduct;
    }

    /**
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     *
     * @return int
     */
    protected function executeCreateStockProductTransaction(StockProductTransfer $transferStockProduct)
    {
        $idStockType = $this->stockReader->getStockTypeIdByName($transferStockProduct->getStockType());
        $idProduct = $this->stockProductReader->getProductConcreteIdBySku($transferStockProduct->getSku());
        $this->stockProductReader->checkStockDoesNotExist($idStockType, $idProduct);
        $idStockProduct = $this->saveStockProduct($transferStockProduct, $idStockType, $idProduct);

        $this->handleStockUpdatePlugins($transferStockProduct->getSku());

        return $idStockProduct;
    }

    /**
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     *
     * @return int
     */
    public function updateStockProduct(StockProductTransfer $transferStockProduct)
    {
        $idStockProduct = $this->getTransactionHandler()->handleTransaction(function () use ($transferStockProduct) {
            return $this->executeUpdateStockProductTransaction($transferStockProduct);
        });

        return $idStockProduct;
    }

    /**
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     *
     * @return int
     */
    protected function executeUpdateStockProductTransaction(StockProductTransfer $transferStockProduct)
    {
        $idProduct = $this->stockProductReader->getProductConcreteIdBySku($transferStockProduct->getSku());
        $idStock = $this->stockReader->getStockTypeIdByName($transferStockProduct->getStockType());
        $stockProductEntity = $this->stockProductReader->getStockProductById($transferStockProduct->getIdStockProduct());

        $stockProductEntity
            ->setFkStock($idStock)
            ->setFkProduct($idProduct)
            ->setQuantity($transferStockProduct->getQuantity())
            ->setIsNeverOutOfStock($transferStockProduct->getIsNeverOutOfStock())
            ->save();

        $this->insertActiveTouchRecordStockProduct($stockProductEntity);
        $this->handleStockUpdatePlugins($transferStockProduct->getSku());

        return $stockProductEntity->getPrimaryKey();
    }

    /**
     * @param string $sku
     * @param string $stockType
     * @param \Spryker\DecimalObject\Decimal $decrementBy
     *
     * @return void
     */
    public function decrementStock($sku, $stockType, Decimal $decrementBy): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($sku, $stockType, $decrementBy) {
            $this->executeDecrementStockTransaction($sku, $stockType, $decrementBy);
        });
    }

    /**
     * @param string $sku
     * @param string $stockType
     * @param \Spryker\DecimalObject\Decimal $decrementBy
     *
     * @return void
     */
    protected function executeDecrementStockTransaction($sku, $stockType, Decimal $decrementBy): void
    {
        $idProduct = $this->stockProductReader->getProductConcreteIdBySku($sku);
        $idStock = $this->stockReader->getStockTypeIdByName($stockType);
        $stockProductEntity = $this->queryContainer
            ->queryStockProductByStockAndProduct($idStock, $idProduct)
            ->findOneOrCreate();

        $stockProductEntity->decrement($decrementBy);
        $stockProductEntity->save();
        $this->insertActiveTouchRecordStockProduct($stockProductEntity);
    }

    /**
     * @param string $sku
     * @param string $stockType
     * @param \Spryker\DecimalObject\Decimal $incrementBy
     *
     * @return void
     */
    public function incrementStock($sku, $stockType, Decimal $incrementBy): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($sku, $stockType, $incrementBy) {
            $this->executeIncrementStockTransaction($sku, $stockType, $incrementBy);
        });
    }

    /**
     * @param string $sku
     * @param string $stockType
     * @param \Spryker\DecimalObject\Decimal $incrementBy
     *
     * @return void
     */
    protected function executeIncrementStockTransaction($sku, $stockType, Decimal $incrementBy): void
    {
        $idProduct = $this->stockProductReader->getProductConcreteIdBySku($sku);
        $idStock = $this->stockReader->getStockTypeIdByName($stockType);

        $stockProductEntity = $this->queryContainer
            ->queryStockProductByStockAndProduct($idStock, $idProduct)
            ->findOneOrCreate();

        $stockProductEntity->increment($incrementBy);
        $stockProductEntity->save();
        $this->insertActiveTouchRecordStockProduct($stockProductEntity);
    }

    /**
     * @param \Orm\Zed\Stock\Persistence\SpyStock $stockTypeEntity
     *
     * @return void
     */
    protected function insertActiveTouchRecordStockType(SpyStock $stockTypeEntity)
    {
        $this->touchFacade->touchActive(
            static::TOUCH_STOCK_TYPE,
            $stockTypeEntity->getIdStock(),
        );
    }

    /**
     * @param \Orm\Zed\Stock\Persistence\SpyStockProduct $stockProductEntity
     *
     * @return void
     */
    protected function insertActiveTouchRecordStockProduct(SpyStockProduct $stockProductEntity)
    {
        $this->touchFacade->touchActive(
            static::TOUCH_STOCK_PRODUCT,
            $stockProductEntity->getIdStockProduct(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     * @param int $idStockType
     * @param int $idProduct
     *
     * @return int
     */
    protected function saveStockProduct(StockProductTransfer $transferStockProduct, $idStockType, $idProduct)
    {
        $stockProductEntity = new SpyStockProduct();
        $stockProductEntity->setFkProduct($idProduct)
            ->setFkStock($idStockType)
            ->setIsNeverOutOfStock($transferStockProduct->getIsNeverOutOfStock())
            ->setQuantity($transferStockProduct->getQuantity())
            ->save();

        $this->insertActiveTouchRecordStockProduct($stockProductEntity);

        return $stockProductEntity->getPrimaryKey();
    }

    /**
     * @param string $sku
     *
     * @return void
     */
    protected function handleStockUpdatePlugins($sku)
    {
        foreach ($this->stockUpdateHandlerPlugins as $stockUpdateHandlerPlugin) {
            $stockUpdateHandlerPlugin->handle($sku);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function persistStockProductCollection(ProductConcreteTransfer $productConcreteTransfer)
    {
        foreach ($productConcreteTransfer->getStocks() as $stockTransfer) {
            if (!$this->stockProductReader->hasStockProduct($stockTransfer->getSku(), $stockTransfer->getStockType())) {
                $this->createStockProduct($stockTransfer);
            } else {
                $idStockProduct = $stockTransfer->getIdStockProduct();
                if (!$idStockProduct) {
                    $idStockProduct = $this->stockProductReader->getIdStockProduct($stockTransfer->getSku(), $stockTransfer->getStockType());
                    $stockTransfer->setIdStockProduct($idStockProduct);
                }
                $this->updateStockProduct($stockTransfer);
            }
        }

        return $productConcreteTransfer;
    }
}

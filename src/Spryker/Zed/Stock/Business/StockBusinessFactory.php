<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Stock\Business\Model\Calculator;
use Spryker\Zed\Stock\Business\Model\Writer;
use Spryker\Zed\Stock\Business\Stock\StockCreator;
use Spryker\Zed\Stock\Business\Stock\StockCreatorInterface;
use Spryker\Zed\Stock\Business\Stock\StockMapper;
use Spryker\Zed\Stock\Business\Stock\StockMapperInterface;
use Spryker\Zed\Stock\Business\Stock\StockReader;
use Spryker\Zed\Stock\Business\Stock\StockReaderInterface;
use Spryker\Zed\Stock\Business\Stock\StockStoreRelationshipUpdater;
use Spryker\Zed\Stock\Business\Stock\StockStoreRelationshipUpdaterInterface;
use Spryker\Zed\Stock\Business\Stock\StockUpdater;
use Spryker\Zed\Stock\Business\Stock\StockUpdaterInterface;
use Spryker\Zed\Stock\Business\StockProduct\StockProductReader;
use Spryker\Zed\Stock\Business\StockProduct\StockProductReaderInterface;
use Spryker\Zed\Stock\Business\StockProduct\StockProductUpdater;
use Spryker\Zed\Stock\Business\StockProduct\StockProductUpdaterInterface;
use Spryker\Zed\Stock\Business\Transfer\StockProductTransferMapper;
use Spryker\Zed\Stock\Dependency\External\StockToConnectionInterface;
use Spryker\Zed\Stock\StockDependencyProvider;

/**
 * @method \Spryker\Zed\Stock\StockConfig getConfig()
 * @method \Spryker\Zed\Stock\Persistence\StockQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Stock\Persistence\StockRepositoryInterface getRepository()
 * @method \Spryker\Zed\Stock\Persistence\StockEntityManagerInterface getEntityManager()
 */
class StockBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\Stock\Business\Model\CalculatorInterface
     */
    public function createCalculatorModel()
    {
        return new Calculator(
            $this->createStockProductReader(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\Stock\StockReaderInterface
     */
    public function createStockReader(): StockReaderInterface
    {
        return new StockReader(
            $this->getRepository(),
            $this->createStockMapper(),
            $this->getStoreFacade(),
            $this->getStockCollectionExpanderPlugins(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\StockProduct\StockProductReaderInterface
     */
    public function createStockProductReader(): StockProductReaderInterface
    {
        return new StockProductReader(
            $this->getProductFacade(),
            $this->createStockReader(),
            $this->getQueryContainer(),
            $this->getRepository(),
            $this->createStockProductTransferMapper(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\Model\WriterInterface
     */
    public function createWriterModel()
    {
        return new Writer(
            $this->getQueryContainer(),
            $this->createStockReader(),
            $this->createStockProductReader(),
            $this->getTouchFacade(),
            $this->getStockUpdateHandlerPlugins(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\Stock\StockCreatorInterface
     */
    public function createStockCreator(): StockCreatorInterface
    {
        return new StockCreator(
            $this->getEntityManager(),
            $this->getTouchFacade(),
            $this->getConnection(),
            $this->getStockPostCreatePlugins(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\Stock\StockUpdaterInterface
     */
    public function createStockUpdater(): StockUpdaterInterface
    {
        return new StockUpdater(
            $this->getEntityManager(),
            $this->getTouchFacade(),
            $this->createStockStoreRelationshipUpdater(),
            $this->createStockProductUpdater(),
            $this->getConnection(),
            $this->getStockPostUpdatePlugins(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\Stock\StockStoreRelationshipUpdaterInterface
     */
    public function createStockStoreRelationshipUpdater(): StockStoreRelationshipUpdaterInterface
    {
        return new StockStoreRelationshipUpdater(
            $this->getRepository(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\StockProduct\StockProductUpdaterInterface
     */
    public function createStockProductUpdater(): StockProductUpdaterInterface
    {
        return new StockProductUpdater(
            $this->getRepository(),
            $this->getStockUpdateHandlerPlugins(),
        );
    }

    /**
     * @return \Spryker\Zed\Stock\Business\Stock\StockMapperInterface
     */
    public function createStockMapper(): StockMapperInterface
    {
        return new StockMapper();
    }

    /**
     * @return \Spryker\Zed\Stock\Business\Transfer\StockProductTransferMapperInterface
     */
    public function createStockProductTransferMapper()
    {
        return new StockProductTransferMapper();
    }

    /**
     * @return \Spryker\Zed\Stock\Dependency\Facade\StockToProductInterface
     */
    public function getProductFacade()
    {
        return $this->getProvidedDependency(StockDependencyProvider::FACADE_PRODUCT);
    }

    /**
     * @return \Spryker\Zed\Stock\Dependency\Facade\StockToTouchInterface
     */
    public function getTouchFacade()
    {
        return $this->getProvidedDependency(StockDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return array<\Spryker\Zed\StockExtension\Dependency\Plugin\StockUpdateHandlerPluginInterface>
     */
    public function getStockUpdateHandlerPlugins()
    {
        return $this->getProvidedDependency(StockDependencyProvider::PLUGINS_STOCK_UPDATE);
    }

    /**
     * @return \Spryker\Zed\Stock\Dependency\Facade\StockToStoreFacadeInterface
     */
    public function getStoreFacade()
    {
        return $this->getProvidedDependency(StockDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \Spryker\Zed\Stock\Dependency\External\StockToConnectionInterface
     */
    public function getConnection(): StockToConnectionInterface
    {
        return $this->getProvidedDependency(StockDependencyProvider::CONNECTION);
    }

    /**
     * @return array<\Spryker\Zed\StockExtension\Dependency\Plugin\StockCollectionExpanderPluginInterface>
     */
    public function getStockCollectionExpanderPlugins(): array
    {
        return $this->getProvidedDependency(StockDependencyProvider::PLUGINS_STOCK_COLLECTION_EXPANDER);
    }

    /**
     * @return array<\Spryker\Zed\StockExtension\Dependency\Plugin\StockPostCreatePluginInterface>
     */
    public function getStockPostCreatePlugins(): array
    {
        return $this->getProvidedDependency(StockDependencyProvider::PLUGINS_STOCK_POST_CREATE);
    }

    /**
     * @return array<\Spryker\Zed\StockExtension\Dependency\Plugin\StockPostUpdatePluginInterface>
     */
    public function getStockPostUpdatePlugins(): array
    {
        return $this->getProvidedDependency(StockDependencyProvider::PLUGINS_STOCK_POST_UPDATE);
    }
}

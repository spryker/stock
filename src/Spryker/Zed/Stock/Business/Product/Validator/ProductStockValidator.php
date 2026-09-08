<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Stock\Business\Product\Validator;

use ArrayObject;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\ProductConcreteCollectionRequestTransfer;
use Generated\Shared\Transfer\ProductConcreteCollectionResponseTransfer;
use Generated\Shared\Transfer\StockConditionsTransfer;
use Generated\Shared\Transfer\StockCriteriaTransfer;
use Spryker\Zed\Stock\Persistence\StockRepositoryInterface;

class ProductStockValidator implements ProductStockValidatorInterface
{
    public function __construct(protected readonly StockRepositoryInterface $stockRepository)
    {
    }

    public function validateProductConcreteCollection(
        ProductConcreteCollectionRequestTransfer $productConcreteCollectionRequestTransfer,
        ProductConcreteCollectionResponseTransfer $productConcreteCollectionResponseTransfer
    ): ProductConcreteCollectionResponseTransfer {
        $stockNamesBySku = [];

        foreach ($productConcreteCollectionRequestTransfer->getProducts() as $productConcreteTransfer) {
            $stockNames = $this->extractStockNames($productConcreteTransfer->getStocks());

            if ($stockNames !== []) {
                $stockNamesBySku[(string)$productConcreteTransfer->getSku()] = $stockNames;
            }
        }

        if ($stockNamesBySku === []) {
            return $productConcreteCollectionResponseTransfer;
        }

        $knownStockNames = $this->getKnownStockNames(
            array_values(array_unique(array_merge(...array_values($stockNamesBySku)))),
        );

        foreach ($stockNamesBySku as $sku => $stockNames) {
            foreach (array_diff($stockNames, $knownStockNames) as $unknownStockName) {
                $productConcreteCollectionResponseTransfer->addError(
                    (new ErrorTransfer())
                        ->setEntityIdentifier((string)$sku)
                        ->setMessage(sprintf('Warehouse (stock) "%s" does not exist.', $unknownStockName)),
                );
            }
        }

        return $productConcreteCollectionResponseTransfer;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\StockProductTransfer> $stockProductTransfers
     *
     * @return list<string>
     */
    protected function extractStockNames(ArrayObject $stockProductTransfers): array
    {
        $stockNames = [];

        foreach ($stockProductTransfers as $stockProductTransfer) {
            $stockName = $stockProductTransfer->getStockType();

            if ($stockName !== null && $stockName !== '') {
                $stockNames[] = $stockName;
            }
        }

        return array_values(array_unique($stockNames));
    }

    /**
     * @param list<string> $stockNames
     *
     * @return list<string>
     */
    protected function getKnownStockNames(array $stockNames): array
    {
        $stockCriteriaTransfer = (new StockCriteriaTransfer())
            ->setStockConditions(
                (new StockConditionsTransfer())->setStockNames($stockNames),
            );

        $knownStockNames = [];

        foreach ($this->stockRepository->getStockCollection($stockCriteriaTransfer)->getStocks() as $stockTransfer) {
            $stockName = $stockTransfer->getName();

            if ($stockName !== null) {
                $knownStockNames[] = $stockName;
            }
        }

        return $knownStockNames;
    }
}

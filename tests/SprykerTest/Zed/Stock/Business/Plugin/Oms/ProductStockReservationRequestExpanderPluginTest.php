<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Stock\Business\Plugin\Oms;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ReservationRequestTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\Stock\Communication\Plugin\Oms\ProductStockReservationRequestExpanderPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Stock
 * @group Business
 * @group Plugin
 * @group Oms
 * @group ProductStockReservationRequestExpanderPluginTest
 *
 * Add your own group annotations below this line
 */
class ProductStockReservationRequestExpanderPluginTest extends Unit
{
    /**
     * @var string
     */
    protected const SKU_UNKNOWN = 'sku-that-does-not-exist-001';

    /**
     * @var \SprykerTest\Zed\Stock\StockBusinessTester
     */
    protected $tester;

    public function testExpandReturnsRequestUnchangedWhenSkuIsNull(): void
    {
        // Arrange
        $reservationRequestTransfer = new ReservationRequestTransfer();

        // Act
        $resultReservationRequestTransfer = $this->createPlugin()->expand($reservationRequestTransfer);

        // Assert
        $this->assertSame($reservationRequestTransfer, $resultReservationRequestTransfer);
        $this->assertCount(0, $resultReservationRequestTransfer->getStores());
    }

    public function testExpandReturnsRequestUnchangedWhenSkuIsEmptyString(): void
    {
        // Arrange
        $reservationRequestTransfer = (new ReservationRequestTransfer())->setSku('');

        // Act
        $resultReservationRequestTransfer = $this->createPlugin()->expand($reservationRequestTransfer);

        // Assert
        $this->assertCount(0, $resultReservationRequestTransfer->getStores());
    }

    public function testExpandLeavesPreExistingStoresUntouchedWhenSkuHasNoStock(): void
    {
        // Arrange
        $preExistingStoreTransfer = (new StoreTransfer())->setName('DE');
        $reservationRequestTransfer = (new ReservationRequestTransfer())
            ->setSku(static::SKU_UNKNOWN)
            ->addStore($preExistingStoreTransfer);

        // Act
        $resultReservationRequestTransfer = $this->createPlugin()->expand($reservationRequestTransfer);

        // Assert
        $this->assertCount(1, $resultReservationRequestTransfer->getStores());
        $this->assertSame(
            'DE',
            $resultReservationRequestTransfer->getStores()->getIterator()->current()->getName(),
        );
    }

    public function testIsApplicableReturnsTrueWhenSkuIsSet(): void
    {
        $reservationRequestTransfer = (new ReservationRequestTransfer())->setSku('any-sku');

        $this->assertTrue($this->createPlugin()->isApplicable($reservationRequestTransfer));
    }

    public function testIsApplicableReturnsFalseWhenSkuIsNull(): void
    {
        $this->assertFalse($this->createPlugin()->isApplicable(new ReservationRequestTransfer()));
    }

    protected function createPlugin(): ProductStockReservationRequestExpanderPlugin
    {
        return new ProductStockReservationRequestExpanderPlugin();
    }
}

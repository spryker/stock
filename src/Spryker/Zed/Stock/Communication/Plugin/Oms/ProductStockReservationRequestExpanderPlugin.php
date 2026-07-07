<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Communication\Plugin\Oms;

use Generated\Shared\Transfer\ReservationRequestTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\OmsExtension\Dependency\Plugin\ReservationRequestExpanderPluginInterface;

/**
 * @method \Spryker\Zed\Stock\Business\StockFacadeInterface getFacade()
 * @method \Spryker\Zed\Stock\Business\StockBusinessFactory getBusinessFactory()
 */
class ProductStockReservationRequestExpanderPlugin extends AbstractPlugin implements ReservationRequestExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Lists every store that carries stock of the requested product and appends each one
     *   to the reservation request, deduplicated by store name.
     * - No-op when `ReservationRequestTransfer.sku` is not set.
     * - Pre-existing entries in `ReservationRequestTransfer.stores` are kept.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ReservationRequestTransfer $reservationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ReservationRequestTransfer
     */
    public function expand(ReservationRequestTransfer $reservationRequestTransfer): ReservationRequestTransfer
    {
        return $this->getBusinessFactory()->createReservationRequestExpander()->expandWithStores($reservationRequestTransfer);
    }

    /**
     * {@inheritDoc}
     * - Returns true when `ReservationRequestTransfer.sku` is set, so the plugin only
     *   runs for product reservations.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ReservationRequestTransfer $reservationRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(ReservationRequestTransfer $reservationRequestTransfer): bool
    {
        return (bool)$reservationRequestTransfer->getSku();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 100;
    }
}

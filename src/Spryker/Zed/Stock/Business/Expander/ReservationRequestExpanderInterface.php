<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\Expander;

use Generated\Shared\Transfer\ReservationRequestTransfer;

interface ReservationRequestExpanderInterface
{
    /**
     * Specification:
     * - Lists every store that carries stock of the requested product and appends each one
     *   to the reservation request, deduplicated by store name.
     * - No-op when `ReservationRequestTransfer.sku` is not set.
     * - Pre-existing entries in `ReservationRequestTransfer.stores` are kept.
     */
    public function expandWithStores(ReservationRequestTransfer $reservationRequestTransfer): ReservationRequestTransfer;
}

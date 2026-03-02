<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Dependency\External;

interface StockToConnectionInterface
{
    public function beginTransaction(): bool;

    public function commit(): bool;

    public function rollBack(): bool;
}

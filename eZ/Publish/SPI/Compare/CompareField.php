<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Compare;

use eZ\Publish\SPI\Persistence\ValueObject;

abstract class CompareField extends ValueObject
{
    /** @var string */
    public $name;

    /** @var mixed */
    public $value;
}

<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\FieldType;

use eZ\Publish\SPI\Comparison\ComparisonData;
use eZ\Publish\SPI\Persistence\Content\FieldValue;

interface Comparable
{
    public function getDataToCompare(FieldValue $value): ComparisonData;
}

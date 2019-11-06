<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content\VersionDiff\FieldType;

use eZ\Publish\SPI\Comparison\ComparisonResult;

/**
 * Returned when there is no ComparisonEngine registered for given FieldType.
 */
class NoComparisonResult implements ComparisonResult
{
}

<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Comparison;

interface ComparisonEngine
{
    public function compareFieldsData(ComparisonData $comparisonDataA, ComparisonData $comparisonDataB): ComparisonResult;

    public function areFieldsDataEqual(ComparisonData $comparisonDataA, ComparisonData $comparisonDataB): bool;
}

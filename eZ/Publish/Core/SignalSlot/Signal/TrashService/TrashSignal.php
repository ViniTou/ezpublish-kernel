<?php

/**
 * TrashSignal class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace eZ\Publish\Core\SignalSlot\Signal\TrashService;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * TrashSignal class.
 */
class TrashSignal extends Signal
{
    /**
     * LocationId.
     *
     * @var mixed
     */
    public $locationId;

    /**
     * Location ID of parent location of the deleted location.
     *
     * @since 5.4.12/6.3.2
     *
     * @var mixed
     */
    public $parentLocationId;

    /**
     * Content id.
     *
     * @var mixed
     */
    public $contentId;
}

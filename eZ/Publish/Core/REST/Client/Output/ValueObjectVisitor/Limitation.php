<?php

/**
 * File containing the Limitation ValueObjectVisitor class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace eZ\Publish\Core\REST\Client\Output\ValueObjectVisitor;

use EzSystems\EzPlatformRestCommon\Output\ValueObjectVisitor;
use EzSystems\EzPlatformRestCommon\Output\Generator;
use EzSystems\EzPlatformRestCommon\Output\Visitor;

/**
 * Limitation value object visitor.
 */
class Limitation extends ValueObjectVisitor
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \EzSystems\EzPlatformRestCommon\Output\Visitor $visitor
     * @param \EzSystems\EzPlatformRestCommon\Output\Generator $generator
     * @param mixed $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $generator->startObjectElement('limitation');

        $generator->startAttribute('identifier', $data->getIdentifier());
        $generator->endAttribute('identifier');

        $generator->startObjectElement('values');
        $generator->startList('values');

        foreach ($data->limitationValues as $limitationValue) {
            $generator->startObjectElement('ref');
            $generator->startAttribute('href', $limitationValue);
            $generator->endAttribute('href');
            $generator->endObjectElement('ref');
        }

        $generator->endList('values');
        $generator->endObjectElement('values');

        $generator->endObjectElement('limitation');
    }
}

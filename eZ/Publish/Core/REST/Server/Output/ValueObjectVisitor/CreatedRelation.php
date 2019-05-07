<?php

/**
 * File containing the CreatedRelation ValueObjectVisitor class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor;

use EzSystems\EzPlatformRestCommon\Output\Generator;
use EzSystems\EzPlatformRestCommon\Output\Visitor;

/**
 * CreatedRelation value object visitor.
 *
 * @todo coverage add unit test
 */
class CreatedRelation extends RestRelation
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \EzSystems\EzPlatformRestCommon\Output\Visitor $visitor
     * @param \EzSystems\EzPlatformRestCommon\Output\Generator $generator
     * @param \eZ\Publish\Core\REST\Server\Values\CreatedRelation $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        parent::visit($visitor, $generator, $data->relation);
        $visitor->setHeader(
            'Location',
            $this->router->generate(
                'ezpublish_rest_loadVersionRelation',
                array(
                    'contentId' => $data->relation->contentId,
                    'versionNumber' => $data->relation->versionNo,
                    'relationId' => $data->relation->relation->id,
                )
            )
        );
        $visitor->setStatus(201);
    }
}

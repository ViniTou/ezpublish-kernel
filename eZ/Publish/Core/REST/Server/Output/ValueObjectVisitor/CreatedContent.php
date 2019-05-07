<?php

/**
 * File containing the CreatedContent ValueObjectVisitor class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor;

use EzSystems\EzPlatformRestCommon\Output\Generator;
use EzSystems\EzPlatformRestCommon\Output\Visitor;

/**
 * CreatedContent value object visitor.
 */
class CreatedContent extends RestContent
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \EzSystems\EzPlatformRestCommon\Output\Visitor $visitor
     * @param \EzSystems\EzPlatformRestCommon\Output\Generator $generator
     * @param \eZ\Publish\Core\REST\Server\Values\CreatedContent $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        parent::visit($visitor, $generator, $data->content);
        $visitor->setHeader(
            'Location',
            $this->router->generate(
                'ezpublish_rest_loadContent',
                array('contentId' => $data->content->contentInfo->id)
            )
        );
        $visitor->setStatus(201);
    }
}

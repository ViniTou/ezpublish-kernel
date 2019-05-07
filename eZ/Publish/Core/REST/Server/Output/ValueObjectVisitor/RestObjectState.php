<?php

/**
 * File containing the RestObjectState ValueObjectVisitor class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor;

use EzSystems\EzPlatformRestCommon\Output\ValueObjectVisitor;
use EzSystems\EzPlatformRestCommon\Output\Generator;
use EzSystems\EzPlatformRestCommon\Output\Visitor;

/**
 * RestObjectState value object visitor.
 */
class RestObjectState extends ValueObjectVisitor
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \EzSystems\EzPlatformRestCommon\Output\Visitor $visitor
     * @param \EzSystems\EzPlatformRestCommon\Output\Generator $generator
     * @param \EzSystems\EzPlatformRestCommon\Values\RestObjectState $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $generator->startObjectElement('ObjectState');
        $visitor->setHeader('Content-Type', $generator->getMediaType('ObjectState'));
        $visitor->setHeader('Accept-Patch', $generator->getMediaType('ObjectStateUpdate'));

        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ezpublish_rest_loadObjectState',
                array('objectStateGroupId' => $data->groupId, 'objectStateId' => $data->objectState->id)
            )
        );
        $generator->endAttribute('href');

        $generator->startValueElement('id', $data->objectState->id);
        $generator->endValueElement('id');

        $generator->startValueElement('identifier', $data->objectState->identifier);
        $generator->endValueElement('identifier');

        $generator->startValueElement('priority', $data->objectState->priority);
        $generator->endValueElement('priority');

        $generator->startObjectElement('ObjectStateGroup');

        $generator->startAttribute(
            'href',
            $this->router->generate('ezpublish_rest_loadObjectStateGroup', array('objectStateGroupId' => $data->groupId))
        );
        $generator->endAttribute('href');

        $generator->endObjectElement('ObjectStateGroup');

        $generator->startValueElement('defaultLanguageCode', $data->objectState->defaultLanguageCode);
        $generator->endValueElement('defaultLanguageCode');

        $generator->startValueElement('languageCodes', implode(',', $data->objectState->languageCodes));
        $generator->endValueElement('languageCodes');

        $this->visitNamesList($generator, $data->objectState->getNames());
        $this->visitDescriptionsList($generator, $data->objectState->getDescriptions());

        $generator->endObjectElement('ObjectState');
    }
}

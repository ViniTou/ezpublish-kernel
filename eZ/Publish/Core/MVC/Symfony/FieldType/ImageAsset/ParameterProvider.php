<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace eZ\Publish\Core\MVC\Symfony\FieldType\ImageAsset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;

class ParameterProvider implements ParameterProviderInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewParameters(Field $field)
    {
        $image = null;

        /** @var $field \eZ\Publish\Core\FieldType\ImageAsset\Value */
        try {
            $contentInfo = $this->contentService->loadContent(
                $field->value->destinationContentId
            );

            $image = $contentInfo->getField('image');
        } catch (NotFoundException | UnauthorizedException $exception) {
        }

        return [
            'image' => $image
        ];
    }
}

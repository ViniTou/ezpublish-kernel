<?php

/**
 * File containing the RemoteId Criterion parser class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\REST\Server\Input\Parser\Criterion;

use EzSystems\EzPlatformRest\Input\BaseParser;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;
use EzSystems\EzPlatformRest\Exceptions;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\RemoteId as ContentRemoteIdCriterion;

/**
 * Parser for RemoteId Criterion.
 */
class ContentRemoteId extends BaseParser
{
    /**
     * Parses input structure to a Criterion object.
     *
     * @param array $data
     * @param \EzSystems\EzPlatformRest\Input\ParsingDispatcher $parsingDispatcher
     *
     * @throws \EzSystems\EzPlatformRest\Exceptions\Parser
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\RemoteId
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher)
    {
        if (!array_key_exists('ContentRemoteIdCriterion', $data)) {
            throw new Exceptions\Parser('Invalid <ContentRemoteIdCriterion> format');
        }

        return new ContentRemoteIdCriterion(explode(',', $data['ContentRemoteIdCriterion']));
    }
}

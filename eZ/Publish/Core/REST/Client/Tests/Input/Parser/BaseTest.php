<?php

/**
 * File containing a BaseTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace eZ\Publish\Core\REST\Client\Tests\Input\Parser;

use eZ\Publish\Core\REST\Server\Tests;
use EzSystems\EzPlatformRestCommon\Input\ParsingDispatcher;

abstract class BaseTest extends Tests\BaseTest
{
    /**
     * Mock for parsing dispatcher.
     *
     * @var \EzSystems\EzPlatformRestCommon\Input\ParsingDispatcher
     */
    protected $parsingDispatcherMock;

    /**
     * Returns the parsing dispatcher mock.
     *
     * @return \EzSystems\EzPlatformRestCommon\Input\ParsingDispatcher
     */
    protected function getParsingDispatcherMock()
    {
        if (!isset($this->parsingDispatcherMock)) {
            $this->parsingDispatcherMock = $this->createMock(ParsingDispatcher::class);
        }

        return $this->parsingDispatcherMock;
    }
}

<?php

/**
 * File containing a test class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\REST\Server\Tests\Input\Parser;

use eZ\Publish\Core\REST\Server\Input\Parser\RelationCreate;

class RelationCreateTest extends BaseTest
{
    /**
     * Tests the RelationCreate parser.
     */
    public function testParse()
    {
        $inputArray = array(
            'Destination' => array(
                '_href' => '/content/objects/42',
            ),
        );

        $relationCreate = $this->getParser();
        $result = $relationCreate->parse($inputArray, $this->getParsingDispatcherMock());

        $this->assertEquals(
            42,
            $result,
            'RelationCreate struct not parsed correctly.'
        );
    }

    /**
     * Test RelationCreate parser throwing exception on missing Destination.
     *
     * @expectedException \EzSystems\EzPlatformRest\Exceptions\Parser
     * @expectedExceptionMessage Missing or invalid 'Destination' element for RelationCreate.
     */
    public function testParseExceptionOnMissingDestination()
    {
        $inputArray = array();

        $relationCreate = $this->getParser();
        $relationCreate->parse($inputArray, $this->getParsingDispatcherMock());
    }

    /**
     * Test RelationCreate parser throwing exception on missing Destination href.
     *
     * @expectedException \EzSystems\EzPlatformRest\Exceptions\Parser
     * @expectedExceptionMessage Missing '_href' attribute for Destination element in RelationCreate.
     */
    public function testParseExceptionOnMissingDestinationHref()
    {
        $inputArray = array(
            'Destination' => array(),
        );

        $relationCreate = $this->getParser();
        $relationCreate->parse($inputArray, $this->getParsingDispatcherMock());
    }

    /**
     * Returns the RelationCreate parser.
     *
     * @return \eZ\Publish\Core\REST\Server\Input\Parser\RelationCreate
     */
    protected function internalGetParser()
    {
        $parser = new RelationCreate();
        $parser->setRequestParser($this->getRequestParserMock());

        return $parser;
    }

    public function getParseHrefExpectationsMap()
    {
        return array(
            array('/content/objects/42', 'contentId', 42),
        );
    }
}

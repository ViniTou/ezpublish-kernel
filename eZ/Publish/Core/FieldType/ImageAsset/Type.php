<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\FieldType\ImageAsset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\API\Repository\Values\Content\Relation;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;

class Type extends FieldType
{
    protected $settingsSchema = [
        'selectionDefaultLocation' => [
            'type' => 'string',
            'default' => null,
        ],
    ];

    protected $validatorConfigurationSchema = [
        'FileSizeValidator' => [
            'maxFileSize' => [
                'type' => 'int',
                'default' => null,
            ],
        ],
    ];

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentService $contentService, ContentTypeService $contentTypeService)
    {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
    }


    /**
     * @see \eZ\Publish\Core\FieldType\FieldType::validateFieldSettings()
     *
     * @param mixed $fieldSettings
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateFieldSettings($fieldSettings)
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            switch ($name) {
                case 'selectionDefaultLocation':
                    if (!is_int($value) && !is_string($value) && $value !== null) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of either null, string or integer",
                            null,
                            ['%setting%' => $name],
                            "[$name]"
                        );
                    }
                    break;
                default:
                    $validationErrors[] = new ValidationError(
                        "Setting '%setting%' is unknown",
                        null,
                        ['%setting%' => $name],
                        "[$name]"
                    );
            }
        }

        return $validationErrors;
    }

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration)
    {
        $validationErrors = [];

        foreach ($validatorConfiguration as $validatorIdentifier => $parameters) {
            switch ($validatorIdentifier) {
                case 'FileSizeValidator':
                    if (!array_key_exists('maxFileSize', $parameters)) {
                        $validationErrors[] = new ValidationError(
                            'Validator %validator% expects parameter %parameter% to be set.',
                            null,
                            array(
                                '%validator%' => $validatorIdentifier,
                                '%parameter%' => 'maxFileSize',
                            ),
                            "[$validatorIdentifier]"
                        );
                        break;
                    }
                    if (!is_int($parameters['maxFileSize']) && $parameters['maxFileSize'] !== null) {
                        $validationErrors[] = new ValidationError(
                            'Validator %validator% expects parameter %parameter% to be of %type%.',
                            null,
                            [
                                '%validator%' => $validatorIdentifier,
                                '%parameter%' => 'maxFileSize',
                                '%type%' => 'integer',
                            ],
                            "[$validatorIdentifier][maxFileSize]"
                        );
                    }
                    break;
                default:
                    $validationErrors[] = new ValidationError(
                        "Validator '%validator%' is unknown",
                        null,
                        ['%validator%' => $validatorIdentifier],
                        "[$validatorIdentifier]"
                    );
            }
        }

        return $validationErrors;
    }

    /**
     * Validates a field based on the validators in the field definition.
     **
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \eZ\Publish\Core\FieldType\ImageAsset\Value $fieldValue The field value for which an action is performed
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        $linkedContent = $this->contentService->loadContent($fieldValue->destinationContentId);
        $linkedContentType = $this->contentTypeService->loadContentType($linkedContent->contentInfo->contentTypeId);
        if ($linkedContentType->identifier !== 'image') {
            $errors[] = new ValidationError(
                'Only content type of image type can be linked. Selected content type is %type%',
                null,
                ['%type%' => $linkedContentType->identifier],
                'destinationContentId'
            );
        }

        foreach ((array)$fieldDefinition->getValidatorConfiguration() as $validatorIdentifier => $parameters) {
            switch ($validatorIdentifier) {
                case 'FileSizeValidator':
                    if (empty($parameters['maxFileSize'])) {
                        // No file size limit
                        break;
                    }

                    /** @var \eZ\Publish\Core\FieldType\Image\Value $imageValueObject */
                    $imageValueObject = $linkedContent->getField('image')->value;
                    // Database stores maxFileSize in MB
                    if (($parameters['maxFileSize'] * 1024 * 1024) < $imageValueObject->fileSize) {
                        $errors[] = new ValidationError(
                            'The file size cannot exceed %size% byte.',
                            'The file size cannot exceed %size% bytes.',
                            ['%size%' => $parameters['maxFileSize']],
                            'fileSize'
                        );
                    }
                    break;
            }
        }

        return $errors;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezimageasset';
    }

    /**
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     *
     * @param \eZ\Publish\Core\FieldType\ImageAsset\Value $value
     *
     * @return string
     */
    public function getName(SPIValue $value)
    {
        throw new \RuntimeException('Name generation provided via NameableField set via "ezpublish.fieldType.nameable" service tag');
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \eZ\Publish\Core\FieldType\ImageAsset\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Returns if the given $value is considered empty by the field type.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isEmptyValue(SPIValue $value)
    {
        return $value->destinationContentId === null;
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param int|string|\eZ\Publish\API\Repository\Values\Content\ContentInfo|\eZ\Publish\Core\FieldType\Relation\Value $inputValue
     *
     * @return \eZ\Publish\Core\FieldType\ImageAsset\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if ($inputValue instanceof ContentInfo) {
            $inputValue = new Value($inputValue->id);
        } elseif (is_int($inputValue) || is_string($inputValue)) { // content id
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \eZ\Publish\Core\FieldType\ImageAsset\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_int($value->destinationContentId) && !is_string($value->destinationContentId)) {
            throw new InvalidArgumentType(
                '$value->destinationContentId',
                'string|int',
                $value->destinationContentId
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     * For this FieldType, the related object's name is returned.
     *
     * @param \eZ\Publish\Core\FieldType\Relation\Value $value
     *
     * @return mixed
     */
    protected function getSortInfo(BaseValue $value)
    {
        return false;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \eZ\Publish\Core\FieldType\ImageAsset\Value $value
     */
    public function fromHash($hash)
    {
        return new Value($hash['destinationContentId']);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \eZ\Publish\Core\FieldType\ImageAsset\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        return ['destinationContentId' => $value->destinationContentId];
    }

    /**
     * Returns whether the field type is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return false;
    }

    /**
     * Returns relation data extracted from value.
     *
     * Not intended for \eZ\Publish\API\Repository\Values\Content\Relation::COMMON type relations,
     * there is an API for handling those.
     *
     * @param \eZ\Publish\Core\FieldType\ImageAsset\Value $fieldValue
     *
     * @return array Hash with relation type as key and array of destination content ids as value.
     *
     * Example:
     * <code>
     *  array(
     *      \eZ\Publish\API\Repository\Values\Content\Relation::LINK => array(
     *          "contentIds" => array( 12, 13, 14 ),
     *          "locationIds" => array( 24 )
     *      ),
     *      \eZ\Publish\API\Repository\Values\Content\Relation::EMBED => array(
     *          "contentIds" => array( 12 ),
     *          "locationIds" => array( 24, 45 )
     *      ),
     *      \eZ\Publish\API\Repository\Values\Content\Relation::FIELD => array( 12 )
     *  )
     * </code>
     */
    public function getRelations(SPIValue $fieldValue)
    {
        $relations = [];
        if ($fieldValue->destinationContentId !== null) {
            $relations[Relation::FIELD] = [$fieldValue->destinationContentId];
        }

        return $relations;
    }
}

<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;

use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type as ContentType;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
use DOMDocument;
use PDO;

class ImageAssetConverter implements Converter
{
    /**
     * Converts data from $value to $storageFieldValue.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $value
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $storageFieldValue
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        $storageFieldValue->dataInt = !empty($value->data['destinationContentId'])
            ? $value->data['destinationContentId']
            : null;
        $storageFieldValue->sortKeyInt = (int)$value->sortKey;
    }

    /**
     * Converts data from $value to $fieldValue.
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $value
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->data = [
            'destinationContentId' => $value->dataInt ?: null,
        ];
        $fieldValue->sortKey = (int)$value->sortKeyInt;
    }

    /**
     * Converts field definition data in $fieldDef into $storageFieldDef.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;
        $fieldValidators = $fieldDef->fieldTypeConstraints->validators;

        $storageDef->dataInt1 = (isset($fieldSettings['selectionDefaultLocation'])
            ? (int)$fieldSettings['selectionDefaultLocation']
            : 0);
        $storageDef->dataInt2 = (isset($fieldValidators['FileSizeValidator']['maxFileSize'])
            ? $fieldValidators['FileSizeValidator']['maxFileSize']
            : 0);

    }

    /**
     * Converts field definition data in $storageDef into $fieldDef.
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        $fieldDef->fieldTypeConstraints->fieldSettings = [
            'selectionDefaultLocation' => $storageDef->dataInt1,
        ];

        $fieldDef->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'validators' => [
                    'FileSizeValidator' => [
                        'maxFileSize' => ($storageDef->dataInt2 != 0
                            ? $storageDef->dataInt2
                            : null),
                    ],
                ],
            ]
        );
    }

    /**
     * Returns the name of the index column in the attribute table.
     *
     * Returns the name of the index column the datatype uses, which is either
     * "sort_key_int" or "sort_key_string". This column is then used for
     * filtering and sorting for this type.
     *
     * @return string
     */
    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}

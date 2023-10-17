<?php

namespace App\DBAL\Types\Enum;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PropertyTypeEnum extends AbstractEnumType
{
    public const TIMESTAMP = 'timestamp';
    public const SET = 'set';
    public const IMAGE_LIST = 'imageList';
    public const STRING = 'string';
    public const DOUBLE = 'double';
    public const BOOLEAN = 'boolean';
    public const INT = 'int';
    public const IMAGE = 'image';

    protected static array $choices = [
        self::TIMESTAMP => __CLASS__ . '::TIMESTAMP',
        self::SET => __CLASS__ . '::SET',
        self::IMAGE_LIST => __CLASS__ . '::IMAGE_LIST',
        self::STRING => __CLASS__ . '::STRING',
        self::DOUBLE => __CLASS__ . '::DOUBLE',
        self::BOOLEAN => __CLASS__ . '::BOOLEAN',
        self::INT => __CLASS__ . '::INT',
        self::IMAGE => __CLASS__ . '::IMAGE',
    ];
}

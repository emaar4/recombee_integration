<?php

namespace App\DBAL\Types\Enum;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ReservedWordsEnum extends AbstractEnumType
{
//    timestamp', 'set', 'imageList', 'string', 'double', 'boolean', 'int', 'image'}, not \"integer\"
    public const ID = 'id';
    public const ITEM_ID = 'itemid';

    protected static array $choices = [
        self::ID => __CLASS__ . '::ID',
        self::ITEM_ID => __CLASS__ . '::ITEM_ID',
    ];
}

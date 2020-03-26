<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

use PHPUnit\Framework\TestCase;

abstract class EntityTestCase extends TestCase
{
    /**
     * @param array<object> $objects
     */
    protected static function assertArrayContainsObjectOfType(string $expectedClass, array $objects): void
    {
        $objectsOfExpectedType = array_filter(
            $objects,
            function ($object) use ($expectedClass) {
                return $object instanceof $expectedClass;
            });

        self::assertNotEmpty($objectsOfExpectedType, 'Expected array to contain object of type ' . $expectedClass);
    }
}

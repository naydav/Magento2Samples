<?php
declare(strict_types=1);

namespace Engine\Test;

use PHPUnit\Framework\Assert;

/**
 * Check that actual data contains all values from expected data
 * But actual data can have more values than expected data
 */
class AssertArrayContains
{
    /**
     * @param array $expected
     * @param array $actual
     * @return void
     */
    public static function assert(array $expected, array $actual)
    {
        foreach ($expected as $key => $value) {
            Assert::assertArrayHasKey(
                $key,
                $actual,
                "Expected value for key '{$key}' is missed"
            );
            if (is_array($value)) {
                self::assert($value, $actual[$key]);
            } else {
                Assert::assertEquals(
                    $value,
                    $actual[$key],
                    "Expected value for key '{$key}' doesn't match"
                );
            }
        }
    }
}

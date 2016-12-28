<?php
namespace Engine\Backend\Test;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
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
            \PHPUnit_Framework_Assert::assertArrayHasKey(
                $key,
                $actual,
                "Expected value for key '{$key}' is missed"
            );
            if (is_array($value)) {
                self::assert($value, $actual[$key]);
            } else {
                \PHPUnit_Framework_Assert::assertEquals(
                    $value,
                    $actual[$key],
                    "Expected value for key '{$key}' doesn't match"
                );
            }
        }
    }
}

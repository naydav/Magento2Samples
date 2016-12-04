<?php
namespace Engine\Location\Test;

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
    public static function assertArrayContains(array $expected, array $actual)
    {
        foreach (array_keys($expected) as $dataKey) {
            if (is_array($expected[$dataKey])) {
                self::assertArrayContains($expected[$dataKey], $actual[$dataKey]);
            } else {
                \PHPUnit_Framework_Assert::assertArrayHasKey(
                    $dataKey,
                    $actual,
                    "Expected value for key '{$dataKey}' is missed"
                );
                \PHPUnit_Framework_Assert::assertEquals(
                    $expected[$dataKey],
                    $actual[$dataKey],
                    "Expected value for key '{$dataKey}' doesn't match"
                );
            }
        }
    }
}

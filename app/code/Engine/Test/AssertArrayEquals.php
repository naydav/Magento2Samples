<?php
declare(strict_types=1);

namespace Engine\Test;

use PHPUnit\Framework\Assert;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertArrayEquals
{
    /**
     * @param array $expected
     * @param array $actual
     * @param array|null $keysToSkip
     * @return void
     * @internal param bool $skipExtensionAttributes
     */
    public static function assert(array $expected, array $actual, array $keysToSkip = null)
    {
        if (null !== $keysToSkip) {
            $actual = self::unsetKeys($actual, $keysToSkip);
        }
        Assert::assertEquals($expected, $actual);
    }

    /**
     * @param array $array
     * @param array $keysToSkip
     * @return array
     */
    private static function unsetKeys(array &$array, array $keysToSkip)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = self::unsetKeys($value, $keysToSkip);
            }
            if (in_array($key, $keysToSkip, true)) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}

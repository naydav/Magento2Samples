<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertStoreSwitcher
{
    /**
     * @param string $content
     * @param bool $expected
     * @return void
     */
    public static function assert($content, $expected = true)
    {
        \PHPUnit_Framework_Assert::assertSelectCount(
            '#store-change-button',
            $expected ? 1: 0,
            $content,
            'Store view change select is ' . ($expected ? '' : 'not ') . 'present'
        );
    }
}

<?php
namespace Engine\Test;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertPageHeader
{
    /**
     * @param string $html
     * @param string $header
     * @return void
     */
    public static function assert($html, $header)
    {
        \PHPUnit_Framework_Assert::assertSelectEquals('h1', $header, 1, $html, 'Page header is wrong');
    }
}

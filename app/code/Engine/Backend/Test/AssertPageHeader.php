<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
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

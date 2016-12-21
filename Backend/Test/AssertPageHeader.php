<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertPageHeader
{
    /**
     * @param string $content
     * @param string $header
     * @return void
     */
    public static function assert($content, $header)
    {
        \PHPUnit_Framework_Assert::assertSelectEquals('h1', $header, 1, $content, 'Page header is wrong');
    }
}

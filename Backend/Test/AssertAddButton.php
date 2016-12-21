<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertAddButton
{
    /**
     * @param string $content
     * @return void
     */
    public static function assert($content)
    {
        \PHPUnit_Framework_Assert::assertSelectCount('#add', 1, $content, 'Add new button is missed');
    }
}

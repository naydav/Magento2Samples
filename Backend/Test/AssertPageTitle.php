<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertPageTitle
{
    /**
     * @param string $content
     * @param string $title
     * @return void
     */
    public static function assert($content, $title)
    {
        \PHPUnit_Framework_Assert::assertSelectRegExp('title', "#^{$title}.*#", 1, $content, 'Meta title is wrong');
    }
}

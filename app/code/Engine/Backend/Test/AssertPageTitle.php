<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertPageTitle
{
    /**
     * @param string $html
     * @param string $title
     * @param string $regexSeparator
     * @return void
     */
    public static function assert($html, $title, $regexSeparator = '~')
    {
        \PHPUnit_Framework_Assert::assertSelectRegExp(
            'title',
            "{$regexSeparator}{$title}.*{$regexSeparator}",
            1,
            $html,
            'Meta title is wrong'
        );
    }
}

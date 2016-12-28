<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertMenuItem
{
    /**
     * @param string $html
     * @param string $itemId
     * @param string $title
     * @param string|null $link
     * @param string $regexSeparator
     * @return void
     */
    public static function assert($html, $itemId, $title, $link = null, $regexSeparator = '~')
    {
        $matcher = [
            'ancestor' => [
                'attributes' => [
                    'data-ui-id' => "menu-{$itemId}",
                ],
            ],
        ];
        if (null === $link) {
            $additionalMatcherCondition = [
                'content' => $title,
            ];
        } else {
            $additionalMatcherCondition = [
                'tag' => 'a',
                'attributes' => [
                    'href' => "regexp:{$regexSeparator}{$link}{$regexSeparator}",
                ],
                'descendant' => [
                    'content' => $title,
                ],
            ];
        }
        $matcher = array_merge($matcher, $additionalMatcherCondition);
        \PHPUnit_Framework_Assert::assertTag($matcher, $html, "Wrong link {$itemId}:{$title}");
    }
}

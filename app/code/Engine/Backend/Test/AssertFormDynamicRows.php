<?php
namespace Engine\Backend\Test;

use Engine\Framework\Test\AssertArrayContains;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertFormDynamicRows
{
    /**
     * @param string $html
     * @param string $form
     * @param string $fieldset
     * @param string $dynamicRows
     * @param array|null $values
     * @return void
     */
    public static function assert($html, $form, $fieldset, $dynamicRows, array $values = null)
    {
        \PHPUnit_Framework_Assert::assertSelectCount('.form-inline', 1, $html, "Form container is missed");

        $pattern = '#<script type="text/x-magento-init">(.*' . $form . '.*)</script>#';
        if (!preg_match($pattern, $html, $matches)) {
            \PHPUnit_Framework_Assert::fail("Form '{$form}' initialization is missed");
        }
        \PHPUnit_Framework_Assert::assertJson($matches[1]);
        $config = json_decode($matches[1], true);

        if (!isset($config['*']['Magento_Ui/js/core/app']['components'][$form]['children'])) {
            \PHPUnit_Framework_Assert::fail("Form '{$form}' configuration is missed");
        }
        $formConfig = $config['*']['Magento_Ui/js/core/app']['components'][$form]['children'];

        if (!isset($formConfig[$form])) {
            \PHPUnit_Framework_Assert::fail("Form '{$form}' structure configuration is missed");
        }
        $formStructureConfig = $formConfig[$form];

        if (!isset($formStructureConfig['children'][$fieldset])) {
            \PHPUnit_Framework_Assert::fail("Fieldset '{$fieldset}' configuration is missed");
        }
        $fieldSetConfig = $formStructureConfig['children'][$fieldset];

        if (!isset($fieldSetConfig['children'][$dynamicRows])) {
            \PHPUnit_Framework_Assert::fail("Dynamic Rows '{$dynamicRows}' configuration is missed");
        }

        if (null !== $values) {
            if (!isset($formConfig[$form . '_data_source']['config']['data'])) {
                \PHPUnit_Framework_Assert::fail("Form '{$form}' data is missed");
            }
            $formData = $formConfig[$form . '_data_source']['config']['data'];

            if (!isset($formData[$fieldset])) {
                \PHPUnit_Framework_Assert::fail("Fieldset '{$fieldset}' data is missed");
            }
            $fieldsetData = $formData[$fieldset];

            if (!isset($fieldsetData[$dynamicRows])) {
                \PHPUnit_Framework_Assert::fail("Dynamic Rows '{$dynamicRows}' data is missed");
            }
            $dynamicRowsData = $fieldsetData[$dynamicRows];

            AssertArrayContains::assert($values, $dynamicRowsData);
        }
    }
}

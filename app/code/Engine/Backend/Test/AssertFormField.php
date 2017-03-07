<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertFormField
{
    /**
     * @param string $html
     * @param string $form
     * @param string $fieldset
     * @param string $field
     * @param string|int|bool|null $value
     * @param string|null $dataNamespace
     * @return void
     */
    public static function assert($html, $form, $fieldset, $field, $value = null, $dataNamespace = null)
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

        if (!isset($fieldSetConfig['children'][$field])) {
            \PHPUnit_Framework_Assert::fail("Field '{$field}' configuration is missed");
        }

        if (null !== $value) {
            if (!isset($formConfig[$form . '_data_source']['config']['data'])) {
                \PHPUnit_Framework_Assert::fail("Form '{$form}' data is missed");
            }
            $formData = $formConfig[$form . '_data_source']['config']['data'];

            if (null === $dataNamespace) {
                if (!isset($formData[$fieldset])) {
                    \PHPUnit_Framework_Assert::fail("Data namespace '{$fieldset}' is missed");
                }
                $fieldsetData = $formData[$fieldset];
            } else {
                $namespaces = explode('/', $dataNamespace);
                if (count($namespaces)) {
                    foreach ($namespaces as $namespace) {
                        $formData = $formData[$namespace];
                    }
                    $fieldsetData = $formData;
                } else {
                    \PHPUnit_Framework_Assert::fail("Fieldset '{$fieldset}' is missed");
                }
            }

            if (!isset($fieldsetData[$field])) {
                 \PHPUnit_Framework_Assert::fail("Field '{$field}' is missed");
            }
            $fieldData = $fieldsetData[$field];

            if ((string)$fieldData !== (string)$value) {
                \PHPUnit_Framework_Assert::fail("Field '{$field}' data '{$fieldData}' is not equal to '{$value}'");
            }
        }
    }
}

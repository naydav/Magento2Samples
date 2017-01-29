<?php
namespace Engine\Category\Test\Api\CategoryRepository;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\Validator\UrlKeyValidator;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidationTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-category/categories';
    const SERVICE_NAME = 'categoryCategoryRepositoryV1';
    /**#@-*/

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    public function setUp()
    {
        parent::setUp();
        $this->rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array $expectedErrorObj
     * @dataProvider validationDataProvider
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testValidationOnCreate($field, $value, array $expectedErrorObj)
    {
        $data = [
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey',
            CategoryInterface::IS_ANCHOR => true,
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 200,
            CategoryInterface::TITLE => 'Category-title',
            CategoryInterface::DESCRIPTION => 'Category-description',
        ];
        $data[$field] = $value;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        try {
            $this->_webApiCall($serviceInfo, ['category' => $data]);
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            self::assertContains(
                $expectedErrorObj['message'],
                $e->getMessage(),
                'SoapFault does not contain expected message.'
            );
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals($expectedErrorObj, $errorData);
            self::assertEquals(Exception::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array $expectedErrorObj
     * @dataProvider validationDataProvider
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testValidationOnUpdate($field, $value, array $expectedErrorObj)
    {
        $categoryId = 100;
        $data = [
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey-updated',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 300,
            CategoryInterface::TITLE => 'Category-title-updated',
            CategoryInterface::DESCRIPTION => 'Category-description-updated',
        ];
        $data[$field] = $value;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $categoryId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        try {
            $this->_webApiCall($serviceInfo, ['category' => $data], null, 'all');
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            self::assertContains(
                $expectedErrorObj['message'],
                $e->getMessage(),
                'SoapFault does not contain expected message.'
            );
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals($expectedErrorObj, $errorData);
            self::assertEquals(Exception::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        /** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
        $rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
        $invalidMaxLengthUrlKey = str_repeat(1, UrlKeyValidator::MAX_URL_KEY_LENGTH + 1);
        return [
            [
                CategoryInterface::PARENT_ID,
                null,
                [
                    'message' => 'Category can\'t has empty parent.',
                ],
            ],
            [
                CategoryInterface::URL_KEY,
                '',
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        CategoryInterface::URL_KEY,
                    ],
                ],
            ],
            [
                CategoryInterface::URL_KEY,
                $invalidMaxLengthUrlKey,
                [
                    'message' => 'Value "%1" for "%2" is more than %3 characters long.',
                    'parameters' => [
                        $invalidMaxLengthUrlKey,
                        CategoryInterface::URL_KEY,
                        UrlKeyValidator::MAX_URL_KEY_LENGTH,
                    ],
                ],
            ],
            [
                CategoryInterface::URL_KEY,
                'Category-urlKey-200',
                [
                    'message' => 'Category with such url "%1" already exist (Category title: %2, Category id: %3, '
                        . 'Parent id: %4).',
                    'parameters' => [
                        'Category-urlKey-200',
                        'Category-title-200',
                        200,
                        $rootCategoryIdProvider->provide(),
                    ],
                ],
            ],
            [
                CategoryInterface::TITLE,
                '',
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        CategoryInterface::TITLE,
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testSetParentForRootCategory()
    {
        $categoryId = $this->rootCategoryIdProvider->provide();
        $data = [
            CategoryInterface::PARENT_ID => 100,
            CategoryInterface::URL_KEY => 'Category-urlKey-updated',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 300,
            CategoryInterface::TITLE => 'Category-title-updated',
            CategoryInterface::DESCRIPTION => 'Category-description-updated',
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $categoryId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        $errorMessage = 'Root Category can\'t has parent.';
        try {
            $this->_webApiCall($serviceInfo, ['category' => $data], null, 'all');
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            self::assertContains(
                $errorMessage,
                $e->getMessage(),
                'SoapFault does not contain expected message.'
            );
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals($errorMessage, $errorData['message']);
            self::assertEquals(Exception::HTTP_BAD_REQUEST, $e->getCode());
        }
    }
}

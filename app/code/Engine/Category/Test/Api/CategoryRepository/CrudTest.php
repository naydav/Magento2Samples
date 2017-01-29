<?php
namespace Engine\Category\Test\Api\CategoryRepository;

use Engine\Test\AssertArrayContains;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CrudTest extends WebapiAbstract
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

    public function testCreate()
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
        $categoryId = $this->_webApiCall($serviceInfo, ['category' => $data]);
        self::assertNotEmpty($categoryId);

        $category = $this->getCategoryById($categoryId);
        AssertArrayContains::assert($data, $category);

        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = Bootstrap::getObjectManager()->get(
            CategoryRepositoryInterface::class
        );
        $categoryRepository->deleteById($categoryId);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $categoryId = 100;
        $data = [
            CategoryInterface::PARENT_ID => 200,
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
        $this->_webApiCall($serviceInfo, ['category' => $data], null, 'all');

        $data[CategoryInterface::CATEGORY_ID] = $categoryId;
        AssertArrayContains::assert(
            $data,
            $this->getCategoryById($categoryId, 'default')
        );
        AssertArrayContains::assert(
            $data,
            $this->getCategoryById($categoryId, 'test_store')
        );
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $categoryId = 100;
        $storeCode = 'test_store';
        $dataForTestStore = [
            CategoryInterface::PARENT_ID => 200,
            CategoryInterface::URL_KEY => 'Category-urlKey-updated',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 300,
            CategoryInterface::TITLE => 'Category-title-per-store',
            CategoryInterface::DESCRIPTION => 'Category-description-per-store',
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
        $this->_webApiCall($serviceInfo, ['category' => $dataForTestStore], null, $storeCode);

        $category = $this->getCategoryById($categoryId, 'default');
        $dataForDefaultStore = array_merge($dataForTestStore, [
            CategoryInterface::TITLE => 'Category-title-100',
            CategoryInterface::DESCRIPTION => 'Category-description-100',
        ]);
        AssertArrayContains::assert($dataForDefaultStore, $category);

        $category = $this->getCategoryById($categoryId, $storeCode);
        AssertArrayContains::assert($dataForTestStore, $category);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100_store_scope.php
     */
    public function testDeleteValueInStoreScope()
    {
        $categoryId = 100;
        $storeCode = 'test_store';
        $data = [
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey-updated',
            CategoryInterface::IS_ANCHOR => true,
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 200,
            CategoryInterface::TITLE => null,
            CategoryInterface::DESCRIPTION => null,
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
        $this->_webApiCall($serviceInfo, ['category' => $data], null, $storeCode);

        $category = $this->getCategoryById($categoryId, $storeCode);
        $expectedData = [
            CategoryInterface::TITLE => 'Category-title-100',
            CategoryInterface::DESCRIPTION => 'Category-description-100',
        ];
        AssertArrayContains::assert($expectedData, $category);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testDeleteById()
    {
        $categoryId = 100;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $categoryId,
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];
        $this->_webApiCall($serviceInfo);

        try {
            $this->getCategoryById($categoryId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals('Category with id "%1" does not exist.', $errorData['message']);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testDeleteRootCategoryById()
    {
        $categoryId = $this->rootCategoryIdProvider->provide();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $categoryId,
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];

        $expectedMessage = 'Root Category can not be deleted.';
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            self::assertContains($expectedMessage, $e->getMessage(), 'SoapFault does not contain expected message.');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals($expectedMessage, $errorData['message']);
            self::assertEquals(Exception::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testGet()
    {
        $categoryId = 100;
        $expectedData = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey-100',
            CategoryInterface::IS_ANCHOR => true,
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 200,
            CategoryInterface::TITLE => 'Category-title-100',
            CategoryInterface::DESCRIPTION => 'Category-description-100',
        ];
        $category = $this->getCategoryById($categoryId);
        AssertArrayContains::assert($expectedData, $category);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100_store_scope.php
     */
    public function testGetIfValueIsPerStore()
    {
        $categoryId = 100;
        $storeCode = 'test_store';
        $expectedData = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey-100',
            CategoryInterface::IS_ANCHOR => true,
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 200,
            CategoryInterface::TITLE => 'Category-title-100-per-store',
            CategoryInterface::DESCRIPTION => 'Category-description-100-per-store',
        ];
        $category = $this->getCategoryById($categoryId, $storeCode);
        AssertArrayContains::assert($expectedData, $category);
    }

    public function testGetNoSuchEntityException()
    {
        $notExistingId = -1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $notExistingId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        $expectedMessage = 'Category with id "%1" does not exist.';
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            self::assertContains($expectedMessage, $e->getMessage(), 'SoapFault does not contain expected message.');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals($expectedMessage, $errorData['message']);
            self::assertEquals($notExistingId, $errorData['parameters'][0]);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @param int $id
     * @param string|null $storeCode
     * @return array|int|string|float|bool Web API call results
     */
    private function getCategoryById($id, $storeCode = null)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, [], null, $storeCode);
        return $response;
    }
}

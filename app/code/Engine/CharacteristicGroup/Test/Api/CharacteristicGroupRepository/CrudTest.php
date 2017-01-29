<?php
namespace Engine\CharacteristicGroup\Test\Api\CharacteristicGroupRepository;

use Engine\Test\AssertArrayContains;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
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
    const RESOURCE_PATH = '/V1/engine-characteristic-group/characteristic-groups';
    const SERVICE_NAME = 'characteristicGroupCharacteristicGroupRepositoryV1';
    /**#@-*/

    public function testCreate()
    {
        $data = [
            CharacteristicGroupInterface::IS_ENABLED => true,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description',
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
        $characteristicGroupId = $this->_webApiCall($serviceInfo, ['characteristicGroup' => $data]);
        self::assertNotEmpty($characteristicGroupId);

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        AssertArrayContains::assert($data, $characteristicGroup);

        /** @var CharacteristicGroupRepositoryInterface $characteristicGroupRepository */
        $characteristicGroupRepository = Bootstrap::getObjectManager()->get(
            CharacteristicGroupRepositoryInterface::class
        );
        $characteristicGroupRepository->deleteById($characteristicGroupId);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $characteristicGroupId = 100;
        $data = [
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-updated',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-updated',
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $characteristicGroupId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $this->_webApiCall($serviceInfo, ['characteristicGroup' => $data], null, 'all');

        $data[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID] = $characteristicGroupId;
        AssertArrayContains::assert(
            $data,
            $this->getCharacteristicGroupById($characteristicGroupId, 'default')
        );
        AssertArrayContains::assert(
            $data,
            $this->getCharacteristicGroupById($characteristicGroupId, 'test_store')
        );
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $characteristicGroupId = 100;
        $storeCode = 'test_store';
        $dataForTestStore = [
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-per-store',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-per-store',
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $characteristicGroupId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $this->_webApiCall($serviceInfo, ['characteristicGroup' => $dataForTestStore], null, $storeCode);

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, 'default');
        $dataForDefaultStore = array_merge($dataForTestStore, [
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-100',
        ]);
        AssertArrayContains::assert($dataForDefaultStore, $characteristicGroup);

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, $storeCode);
        AssertArrayContains::assert($dataForTestStore, $characteristicGroup);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100_store_scope.php
     */
    public function testDeleteValueInStoreScope()
    {
        $characteristicGroupId = 100;
        $storeCode = 'test_store';
        $data = [
            CharacteristicGroupInterface::IS_ENABLED => true,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
            CharacteristicGroupInterface::TITLE => null,
            CharacteristicGroupInterface::DESCRIPTION => null,
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $characteristicGroupId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $this->_webApiCall($serviceInfo, ['characteristicGroup' => $data], null, $storeCode);

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, $storeCode);
        $expectedData = [
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-100',
        ];
        AssertArrayContains::assert($expectedData, $characteristicGroup);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testDeleteById()
    {
        $characteristicGroupId = 100;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $characteristicGroupId,
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];
        $this->_webApiCall($serviceInfo);

        try {
            $this->getCharacteristicGroupById($characteristicGroupId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals('Characteristic Group with id "%1" does not exist.', $errorData['message']);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testGet()
    {
        $characteristicGroupId = 100;
        $expectedData = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => true,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-100',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-100',
        ];
        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        AssertArrayContains::assert($expectedData, $characteristicGroup);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100_store_scope.php
     */
    public function testGetIfValueIsPerStore()
    {
        $characteristicGroupId = 100;
        $storeCode = 'test_store';
        $expectedData = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => true,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-100',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100-per-store',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-100-per-store',
        ];
        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, $storeCode);
        AssertArrayContains::assert($expectedData, $characteristicGroup);
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

        $expectedMessage = 'Characteristic Group with id "%1" does not exist.';
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
    private function getCharacteristicGroupById($id, $storeCode = null)
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

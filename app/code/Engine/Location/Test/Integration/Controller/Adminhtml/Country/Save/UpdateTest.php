<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\CountryRepositoryInterface;
use Engine\Test\AssertArrayContains;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class UpdateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/country/save/back/edit';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->countryRepository = $this->_objectManager->get(
            CountryRepositoryInterface::class
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testUpdate()
    {
        $countryId = 100;
        $data = [
            CountryInterface::COUNTRY_ID => $countryId,
            CountryInterface::ENABLED => false,
            CountryInterface::POSITION => 200,
            CountryInterface::NAME => 'Country-name-updated',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-location/country/edit/country_id/'
                . $countryId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Country has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $country = $this->countryRepository->get($countryId);
        AssertArrayContains::assert($data, $this->hydrator->extract($country));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CountryInterface::COUNTRY_ID => 100,
                CountryInterface::ENABLED => false,
            ],
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testUpdateWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CountryInterface::COUNTRY_ID => -1,
            ],
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages(
            $this->contains('The Country does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}

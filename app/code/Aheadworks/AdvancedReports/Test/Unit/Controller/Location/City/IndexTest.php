<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Test\Unit\Controller\Adminhtml\Location\City;

use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReports\Controller\Adminhtml\Location\City\Index;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;

/**
 * Test for \Aheadworks\AdvancedReports\Controller\Adminhtml\Location\City\Index
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IndexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Index
     */
    private $controller;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'resultRedirectFactory' => $resultRedirectFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Index::class,
            [
                'context' => $contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $countryId = 'US';
        $region = base64_encode('Texas');

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('country_id')
            ->willReturn($countryId);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('region')
            ->willReturn($region);

        $titleMock = $this->getMockBuilder(Title::class)
            ->setMethods(['prepend'])
            ->disableOriginalConstructor()
            ->getMock();
        $titleMock->expects($this->once())
            ->method('prepend')
            ->willReturnSelf();
        $pageConfigMock = $this->getMockBuilder(Config::class)
            ->setMethods(['getTitle'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);
        $resultPageMock = $this->getMockBuilder(Page::class)
            ->setMethods(['setActiveMenu', 'getConfig'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultPageMock->expects($this->any())
            ->method('setActiveMenu')
            ->willReturnSelf();
        $resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($pageConfigMock);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->assertSame($resultPageMock, $this->controller->execute());
    }

    /**
     * Testing of redirection if country_id in request is not exists
     */
    public function testExecuteRedirectProductIdInRequestNotExists()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('country_id')
            ->willReturn(null);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('region')
            ->willReturn(null);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/location/index')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }
}

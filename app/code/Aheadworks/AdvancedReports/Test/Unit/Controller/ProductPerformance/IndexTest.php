<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Test\Unit\Controller\Adminhtml\ProductPerformance;

use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReports\Controller\Adminhtml\ProductPerformance\Index;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;

/**
 * Test for \Aheadworks\AdvancedReports\Controller\Adminhtml\ProductPerformance\Index
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
        $contextMock = $objectManager->getObject(
            Context::class,
            ['request' => $this->requestMock]
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
     *
     * @param [] $params
     * @dataProvider executeDataProvider
     */
    public function testExecute($params)
    {
        $this->requestMock->expects($this->exactly(4))
            ->method('getParam')
            ->willReturnMap($params);

        $title = __('Product Performance');
        if ($params[0][2]) {
            $title = __('Product Performance (%1)', base64_decode($params[0][2]));
        }

        $titleMock = $this->getMockBuilder(Title::class)
            ->setMethods(['prepend'])
            ->disableOriginalConstructor()
            ->getMock();
        $titleMock->expects($this->once())
            ->method('prepend')
            ->with($title)
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
     * Data provider for testExecute method
     *
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [
                [
                    ['payment_name', null, base64_encode('Check Money Order')],
                    ['coupon_code', null, null],
                    ['manufacturer', null, null],
                    ['category_name', null, null]
                ]
            ],
            [
                [
                    ['coupon_code', null, base64_encode('rf123456')],
                    ['payment_name', null, null],
                    ['manufacturer', null, null],
                    ['category_name', null, null]
                ]
            ],
            [
                [
                    ['manufacturer', null, base64_encode('Manufacturer 1')],
                    ['payment_name', null, null],
                    ['coupon_code', null, null],
                    ['category_name', null, null]
                ]
            ],
            [
                [
                    ['category_name', null, base64_encode('Category 1')],
                    ['payment_name', null, null],
                    ['coupon_code', null, null],
                    ['manufacturer', null, null]
                ]
            ],
            [
                [
                    ['payment_name', null, null],
                    ['coupon_code', null, null],
                    ['manufacturer', null, null],
                    ['category_name', null, null]
                ]
            ]
        ];
    }
}

<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Test\Unit\Block;

use Aheadworks\AdvancedReports\Block\Ajax;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Ajax
 */
class AjaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Ajax
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Context
     */
    private $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UrlInterface
     */
    private $urlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestInterface
     */
    private $requestMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getUrlBuilder',
                    'getRequest',
                ]
            )
            ->getMock();

        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getUrl',
                ]
            )
            ->getMockForAbstractClass();
        $this->contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isSecure',
                ]
            )
            ->getMockForAbstractClass();
        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $data = [
            'context' => $this->contextMock,
            'data' => [],
        ];

        $this->object = $objectManager->getObject(Ajax::class, $data);
    }

    /**
     * Test getScriptOptions method
     */
    public function testGetScriptOptions()
    {
        $isSecure = false;
        $url = 'https://ecommerce.aheadworks.com/aw_advancedreports/countViews/product/id/14/';
        $expected = '{"url":"https:\/\/ecommerce.aheadworks.com\/aw_advancedreports\/countViews\/product\/id\/14\/"}';

        $this->requestMock->expects($this->once())
            ->method('isSecure')
            ->willReturn($isSecure);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                'aw_advancedreports/countViews/product/',
                [
                    '_current' => true,
                    '_secure' => $isSecure,
                ]
            )->willReturn($url);

        $this->assertEquals($expected, $this->object->getScriptOptions());
    }
}

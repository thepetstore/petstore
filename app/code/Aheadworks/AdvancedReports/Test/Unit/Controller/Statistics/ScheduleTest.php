<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Test\Unit\Controller\Adminhtml\Statistics;

use Aheadworks\AdvancedReports\Controller\Adminhtml\Statistics\Schedule;
use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as StatisticsProcessor;
use Magento\Backend\App\Action\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;

/**
 * Test for \Aheadworks\AdvancedReports\Controller\Adminhtml\Statistics\ScheduleTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ScheduleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Schedule
     */
    private $controller;

    /**
     * @var StatisticsProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticsProcessorMock;

    /**
     * @var Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->statisticsProcessorMock = $this->getMockBuilder(StatisticsProcessor::class)
            ->setMethods(['markIndexerAsInvalid'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setRefererUrl'])
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
                'resultRedirectFactory' => $resultRedirectFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Schedule::class,
            [
                'context' => $contextMock,
                'statisticsProcessor' => $this->statisticsProcessorMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $this->statisticsProcessorMock ->expects($this->once())
            ->method('markIndexerAsInvalid')
            ->willReturnSelf();
        $this->resultRedirectMock->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }
}

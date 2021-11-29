<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Flag;
use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as StatisticsProcessor;

/**
 * Class IndexInfo
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 */
class IndexInfo extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/index-info.phtml';

    /**
     * @var StatisticsProcessor
     */
    private $statisticsProcessor;

    /**
     * @var Flag
     */
    private $flag;

    /**
     * @param Context $context
     * @param Flag $flag
     * @param StatisticsProcessor $statisticsProcessor
     * @param array $data
     */
    public function __construct(
        Context $context,
        Flag $flag,
        StatisticsProcessor $statisticsProcessor,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->flag = $flag;
        $this->statisticsProcessor = $statisticsProcessor;
    }

    /**
     * Show the date of last update reports index
     *
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function showLastIndexUpdate()
    {
        $updatedAt = 'undefined';
        $flag = $this->flag->setReportFlagCode(Flag::AW_AREP_STATISTICS_FLAG_CODE)->loadSelf();
        if ($flag->hasData()) {
            $updatedAt =  $this->_localeDate->formatDate(
                $flag->getLastUpdate(),
                \IntlDateFormatter::MEDIUM,
                true
            );
        }

        return __('The latest Advanced Reports index was updated on %1.', $updatedAt);
    }

    /**
     * Can show index update text
     *
     * @return bool
     */
    public function canShowScheduleMessage()
    {
        if ($this->_authorization->isAllowed('Aheadworks_AdvancedReports::reports_statistics')
            && !$this->statisticsProcessor->isReindexScheduled()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Index update url
     *
     * @return string
     */
    public function getIndexUpdateUrl()
    {
        return $this->getUrl('advancedreports/statistics/schedule');
    }
}

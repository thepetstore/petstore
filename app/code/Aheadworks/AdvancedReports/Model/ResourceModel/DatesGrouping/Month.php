<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\AbstractResource;

/**
 * Class Month
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
class Month extends AbstractResource
{
    /**
     * @var string
     */
    const KEY = 'month';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_month', 'start_date');
    }

    /**
     * {@inheritdoc}
     */
    public function updateTable()
    {
        $maxMonthDateStr = $this->getConnection()->fetchOne('SELECT MAX(end_date) FROM ' . $this->getMainTable());
        $fromDate = $this->getFromDate($maxMonthDateStr);
        $toDate = $this->getToDate();

        $intervals = [];
        while ($fromDate < $toDate) {
            // If main table is empty
            if (!$maxMonthDateStr) {
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('last day of');
                $endDate = $fromDate->format('Y-m-d');
                $fromDate->modify('first day of');
                $fromDate->modify('+1 month');
            } else {
                $fromDate->modify('first day of');
                $fromDate->modify('+1 month');
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('last day of');
                $endDate = $fromDate->format('Y-m-d');
            }
            $intervals[] = ['start_date' => $startDate, 'end_date' => $endDate];
        }
        $this->addPeriodToTable($this->getMainTable(), $intervals);
    }
}

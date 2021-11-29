<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Ui\DataProvider;

use Magento\Framework\DataObject;

/**
 * Class Metadata
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class Metadata extends DataObject implements MetadataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const NAME_SPACE = 'namespace';
    const DEFAULT_FILTER_APPLIERS = 'default_filter_appliers';
    const INDIVIDUAL_FILTER_APPLIERS = 'individual_filter_appliers';
    const COMPARE_ROWS_MERGER = 'compare_rows_merger';
    const COMPARE_ROWS_MERGER_CONFIG = 'compare_rows_merger_config';
    const COMPARE_CHARTS_MERGER = 'compare_charts_merger';
    const COMPARE_CHARTS_MERGER_CONFIG = 'compare_charts_merger_config';
    const COMPARE_TOTALS_MERGER = 'compare_totals_merger';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->getData(self::NAME_SPACE);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilterAppliers()
    {
        return $this->getData(self::DEFAULT_FILTER_APPLIERS);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndividualFilterAppliers()
    {
        return $this->getData(self::INDIVIDUAL_FILTER_APPLIERS);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompareRowsMerger()
    {
        return $this->getData(self::COMPARE_ROWS_MERGER);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompareRowsMergerConfig()
    {
        return $this->getData(self::COMPARE_ROWS_MERGER_CONFIG);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompareChartsMerger()
    {
        return $this->getData(self::COMPARE_CHARTS_MERGER);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompareChartsMergerConfig()
    {
        return $this->getData(self::COMPARE_CHARTS_MERGER_CONFIG);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompareTotalsMerger()
    {
        return $this->getData(self::COMPARE_TOTALS_MERGER);
    }
}

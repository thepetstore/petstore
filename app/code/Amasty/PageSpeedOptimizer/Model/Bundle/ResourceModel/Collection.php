<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \Amasty\PageSpeedOptimizer\Model\Bundle\Bundle::class,
            \Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\Bundle::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}

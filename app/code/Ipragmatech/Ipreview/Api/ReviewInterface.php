<?php
namespace Ipragmatech\Ipreview\Api;

/**
 * Interface ReviewInterface
 * @api
 */
interface ReviewInterface
{
    /**
     * Return Added review item.
     *
     * @param int $productId
     * @return array
     *
     */
    public function getReviewsList($productId);
    /**
     * Return Added review item.
     *
    /**
     * Get Products of
     * @param int $categoryId
     * @param int $pageSize
     * @param int $currPage
     * @param string $orderAs
     * @param string $orderBy
     * @return int|boolean|string|array
     */
    public function getCategoryReviewsList($categoryId,$pageSize,$currPage,$orderAs,$orderBy);

    /**
     * Return Rating options.
     *
     * @param int $storeId
     * @return array
     *
     */
    public function getRatings($storeId = null);

    /**
     * Added review and rating for the product.
     * @param int $productId
     * @param string $title
     * @param string $nickname
     * @param string $detail
     * @param Ipragmatech\Ipreview\Api\Data\RatingInterface[] $ratingData
     * @param int $customer_id
     * @param int $store_id
     * @return boolean
     *
     */
    public function writeReviews(
        $productId,
        $nickname,
        $title,
        $detail,
        $ratingData,
        $customer_id = null,
        $storeId
    );
}

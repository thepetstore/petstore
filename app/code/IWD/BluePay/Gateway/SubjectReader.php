<?php
/**
 * Copyright © 2018 IWD Agency - All rights reserved.
 * See LICENSE.txt bundled with this module for license details.
 */
namespace IWD\BluePay\Gateway;

use IWD\BluePay\Api\AdapterResponseInterface;
use Magento\Payment\Gateway\Helper;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

/**
 * Class SubjectReader
 */
class SubjectReader
{
    /**
     * Reads response object from subject
     *
     * @param array $subject
     * @return object
     */
    public function readResponseObject(array $subject)
    {
        $response = Helper\SubjectReader::readResponse($subject);
        if (!isset($response['object']) || !is_object($response['object'])) {
            throw new \InvalidArgumentException('Response object does not exist');
        }

        return $response['object'];
    }

    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject)
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * Reads transaction from subject
     *
     * @param array $subject
     * @return string
     */
    public function readTransaction(array $subject)
    {
        if (!isset($subject['object']) || !is_object($subject['object'])) {
            throw new \InvalidArgumentException('Response object does not exist');
        }
        if (!($subject['object'] instanceof AdapterResponseInterface)
            || empty($subject['object']->getTransactionId())
        ) {
            throw new \InvalidArgumentException('The object is not contain transaction ID');
        }

        return $subject['object']->getTransactionId();
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return Helper\SubjectReader::readAmount($subject);
    }

    /**
     * Reads customer id from subject
     *
     * @param array $subject
     * @return int
     */
    public function readCustomerId(array $subject)
    {
        if (!isset($subject['customer_id'])) {
            throw new \InvalidArgumentException('The "customerId" field does not exists');
        }

        return (int) $subject['customer_id'];
    }

    /**
     * Reads public hash from subject
     *
     * @param array $subject
     * @return string
     */
    public function readPublicHash(array $subject)
    {
        if (empty($subject[PaymentTokenInterface::PUBLIC_HASH])) {
            throw new \InvalidArgumentException('The "public_hash" field does not exists');
        }

        return $subject[PaymentTokenInterface::PUBLIC_HASH];
    }
}

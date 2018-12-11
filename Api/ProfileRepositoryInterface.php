<?php

namespace ClawRock\Debug\Api;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Model\Profile\Criteria;

interface ProfileRepositoryInterface
{
    /**
     * @param \ClawRock\Debug\Api\Data\ProfileInterface $profile
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(string $token): ProfileInterface;

    /**
     * @param \ClawRock\Debug\Api\Data\ProfileInterface $profile
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(string $token): ProfileRepositoryInterface;

    public function find(Criteria $criteria): array;

    /**
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function findLatest(): ProfileInterface;
}

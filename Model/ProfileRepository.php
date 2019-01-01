<?php

declare(strict_types=1);

namespace ClawRock\Debug\Model;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Model\Profile\Criteria;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProfileRepository implements ProfileRepositoryInterface
{
    /**
     * @var \ClawRock\Debug\Model\Storage\ProfileFileStorage
     */
    private $fileStorage;

    /**
     * @var \ClawRock\Debug\Model\Profile\CriteriaFactory
     */
    private $criteriaFactory;

    public function __construct(
        \ClawRock\Debug\Model\Storage\ProfileFileStorage $fileStorage,
        \ClawRock\Debug\Model\Profile\CriteriaFactory $criteriaFactory
    ) {
        $this->fileStorage = $fileStorage;
        $this->criteriaFactory = $criteriaFactory;
    }

    /**
     * @param \ClawRock\Debug\Api\Data\ProfileInterface $profile
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProfileInterface $profile): ProfileRepositoryInterface
    {
        try {
            $this->fileStorage->write($profile);

            return $this;
        } catch (FileSystemException $e) {
            throw new CouldNotSaveException(__('Profile could not be saved.'));
        }
    }

    /**
     * @param string $token
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(string $token): ProfileInterface
    {
        try {
            return $this->fileStorage->read($token);
        } catch (FileSystemException $e) {
            throw new NoSuchEntityException(__('Profile with token %s doesn\'t exist.', $token));
        }
    }

    /**
     * @param \ClawRock\Debug\Api\Data\ProfileInterface $profile
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ProfileInterface $profile): ProfileRepositoryInterface
    {
        try {
            $this->fileStorage->remove($profile->getToken());

            return $this;
        } catch (FileSystemException $e) {
            throw new CouldNotDeleteException(__('Profile with token %s could not be deleted.', $profile->getToken()));
        }
    }

    /**
     * @param string $token
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(string $token): ProfileRepositoryInterface
    {
        try {
            $this->fileStorage->remove($token);

            return $this;
        } catch (FileSystemException $e) {
            throw new CouldNotDeleteException(__('Profile with token %s could not be deleted.', $token));
        }
    }

    public function find(Criteria $criteria): array
    {
        return $this->fileStorage->find($criteria);
    }

    /**
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function findLatest(): ProfileInterface
    {
        try {
            /** @var \ClawRock\Debug\Model\Profile\Criteria $criteria */
            $criteria = $this->criteriaFactory->create(['limit' => 1]);

            $results = $this->fileStorage->find($criteria);
            $token = reset($results)->getToken();

            return $this->fileStorage->read($token);
        } catch (FileSystemException $e) {
            throw new NoSuchEntityException(__('Could not find latest token'));
        }
    }
}

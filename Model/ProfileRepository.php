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
    private \ClawRock\Debug\Model\Storage\ProfileFileStorage $fileStorage;
    private \ClawRock\Debug\Model\Profile\CriteriaFactory $criteriaFactory;

    public function __construct(
        \ClawRock\Debug\Model\Storage\ProfileFileStorage $fileStorage,
        \ClawRock\Debug\Model\Profile\CriteriaFactory $criteriaFactory
    ) {
        $this->fileStorage = $fileStorage;
        $this->criteriaFactory = $criteriaFactory;
    }

    public function save(ProfileInterface $profile): void
    {
        try {
            $this->fileStorage->write($profile);
        } catch (FileSystemException $e) {
            throw new CouldNotSaveException(__('Profile could not be saved.'));
        }
    }

    public function getById(string $token): ProfileInterface
    {
        try {
            return $this->fileStorage->read($token);
        } catch (FileSystemException $e) {
            throw new NoSuchEntityException(__('Profile with token %s doesn\'t exist.', $token));
        }
    }

    public function delete(ProfileInterface $profile): void
    {
        try {
            $this->fileStorage->remove($profile->getToken());
        } catch (FileSystemException $e) {
            throw new CouldNotDeleteException(__('Profile with token %s could not be deleted.', $profile->getToken()));
        }
    }

    public function deleteById(string $token): void
    {
        try {
            $this->fileStorage->remove($token);
        } catch (FileSystemException $e) {
            throw new CouldNotDeleteException(__('Profile with token %s could not be deleted.', $token));
        }
    }

    public function find(Criteria $criteria): array
    {
        return $this->fileStorage->find($criteria);
    }

    public function findLatest(): ProfileInterface
    {
        try {
            /** @var \ClawRock\Debug\Model\Profile\Criteria $criteria */
            $criteria = $this->criteriaFactory->create(['limit' => 1]);

            $results = $this->fileStorage->find($criteria);
            $latestKey = array_key_first($results);
            if ($latestKey === null) {
                throw new NoSuchEntityException(__('Could not find latest token'));
            }
            $token = $results[$latestKey]->getToken();

            return $this->fileStorage->read($token);
        } catch (FileSystemException $e) {
            throw new NoSuchEntityException(__('Could not find latest token'));
        }
    }
}

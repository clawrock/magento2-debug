<?php

namespace ClawRock\Debug\Plugin\ProfileRepository;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Model\Collector\TimeCollector;

class RequestTimePlugin
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \ClawRock\Debug\Api\ProfileRepositoryInterface $subject
     * @param \ClawRock\Debug\Api\Data\ProfileInterface      $profile
     * @return array
     */
    public function beforeSave(ProfileRepositoryInterface $subject, ProfileInterface $profile)
    {
        try {
            /** @var \ClawRock\Debug\Model\Collector\TimeCollector $timeCollector */
            $timeCollector = $profile->getCollector(TimeCollector::NAME);
        } catch (\InvalidArgumentException $e) {
            return [$profile];
        }

        $profile->setRequestTime($timeCollector->getDuration());

        return [$profile];
    }
}

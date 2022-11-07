<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Serializer;

use ClawRock\Debug\Api\Data\ProfileInterface;

class ProfileSerializer
{
    private \ClawRock\Debug\Serializer\SerializerInterface $serializer;
    private \ClawRock\Debug\Model\Serializer\CollectorSerializer $collectorSerializer;
    private \ClawRock\Debug\Model\ProfileFactory $profileFactory;

    public function __construct(
        \ClawRock\Debug\Serializer\SerializerInterface $serializer,
        \ClawRock\Debug\Model\Serializer\CollectorSerializer $collectorSerializer,
        \ClawRock\Debug\Model\ProfileFactory $profileFactory
    ) {
        $this->serializer = $serializer;
        $this->collectorSerializer = $collectorSerializer;
        $this->profileFactory = $profileFactory;
    }

    public function serialize(ProfileInterface $profile): string
    {
        return $this->serializer->serialize(array_merge(
            $profile->getData(),
            ['collectors' => $this->collectorSerializer->serialize($profile->getCollectors())]
        ));
    }

    public function unserialize(string $data): ProfileInterface
    {
        $profileData = $this->serializer->unserialize($data);
        $collectors = $this->collectorSerializer->unserialize($profileData['collectors']);
        unset($profileData['collectors']);

        /** @var \ClawRock\Debug\Model\Profile $profile */
        $profile = $this->profileFactory->create(['token' => $profileData['token']])->setData($profileData);
        $profile->setCollectors($collectors);

        return $profile;
    }
}

<?php

namespace ClawRock\Debug\Model\Storage;

use ClawRock\Debug\Api\Data\ProfileInterface;

class ProfileMemoryStorage
{
    /**
     * @var \ClawRock\Debug\Model\Profile
     */
    private $profile;

    public function read(): ProfileInterface
    {
        return $this->profile;
    }

    public function write(ProfileInterface $profile)
    {
        $this->profile = $profile;
    }
}

<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Storage;

use ClawRock\Debug\Api\Data\ProfileInterface;

class ProfileMemoryStorage
{
    private \ClawRock\Debug\Api\Data\ProfileInterface $profile;

    public function read(): ProfileInterface
    {
        return $this->profile;
    }

    public function write(ProfileInterface $profile): void
    {
        $this->profile = $profile;
    }
}

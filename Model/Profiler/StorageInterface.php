<?php

namespace ClawRock\Debug\Model\Profiler;

use ClawRock\Debug\Model\Profile;

interface StorageInterface
{
    public function find($ip, $url, $limit, $method, $start = null, $end = null);

    public function read($token);

    public function write(Profile $profile);

    public function purge();
}

<?php

namespace ClawRock\Debug\Model\Profiler;

use ClawRock\Debug\Model\Profile;

class FileStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $folder;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    public function __construct(\Magento\Framework\App\Filesystem\DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
        $this->folder = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'debug';

        if (!is_dir($this->folder) && false === @mkdir($this->folder, 0777, true) && !is_dir($this->folder)) {
            throw new \RuntimeException(sprintf('Unable to create the storage directory (%s).', $this->folder));
        }
    }

    public function find($ip, $url, $limit, $method, $start = null, $end = null, $statusCode = null)
    {
        $file = $this->getIndexFilename();

        if (!file_exists($file)) {
            return [];
        }

        $file = fopen($file, 'r');
        fseek($file, 0, SEEK_END);

        $result = [];
        while (count($result) < $limit && $line = $this->readLineFromFile($file)) {
            $values = str_getcsv($line);
            list($csvToken, $csvIp, $csvMethod, $csvUrl, $csvTime, $csvParent, $csvStatusCode) = $values;
            $fileSize = isset($values[7]) ? $values[7] : 0;

            $csvTime = (int)$csvTime;

            if ($ip && false === strpos($csvIp, $ip) || $url && false === strpos($csvUrl, $url) || $method && false === strpos($csvMethod, $method) || $statusCode && false === strpos($csvStatusCode, $statusCode)) {
                continue;
            }

            if (!empty($start) && $csvTime < $start) {
                continue;
            }

            if (!empty($end) && $csvTime > $end) {
                continue;
            }

            $result[$csvToken] = [
                'token'       => $csvToken,
                'ip'          => $csvIp,
                'method'      => $csvMethod,
                'url'         => $csvUrl,
                'time'        => $csvTime,
                'parent'      => $csvParent,
                'status_code' => $csvStatusCode,
                'size'        => $fileSize,
            ];
        }

        fclose($file);

        return array_values($result);
    }

    public function purge()
    {
        $flags    = \FilesystemIterator::SKIP_DOTS;
        $iterator = new \RecursiveDirectoryIterator($this->folder, $flags);
        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            if (is_file($file)) {
                unlink($file);
            } else {
                rmdir($file);
            }
        }
    }

    public function read($token)
    {
        if (!$token || !file_exists($file = $this->getFilename($token))) {
            return;
        }

        return $this->createProfileFromData($token, unserialize(file_get_contents($file)));
    }

    public function write(Profile $profile)
    {
        $file = $this->getFilename($profile->getToken());

        $profileIndexed = is_file($file);
        if (!$profileIndexed) {
            // Create directory
            $dir = dirname($file);
            if (!is_dir($dir) && false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to create the storage directory (%s).', $dir));
            }
        }

        $data = [
            'token'        => $profile->getToken(),
            'parent'       => $profile->getParentToken(),
            'children'     => array_map(function (Profile $profile) {
                return $profile->getToken();
            }, $profile->getChildren()),
            'data'         => $profile->getCollectors(),
            'ip'           => $profile->getIp(),
            'method'       => $profile->getMethod(),
            'url'          => $profile->getUrl(),
            'time'         => $profile->getTime(),
            'status_code'  => $profile->getStatusCode(),
            'collect_time' => $profile->getCollectTime(),
        ];

        if (false === file_put_contents($file, serialize($data))) {
            return false;
        }

        if (!$profileIndexed) {
            $profileSize = filesize($file);

            if (false === $file = fopen($this->getIndexFilename(), 'a')) {
                return false;
            }

            fputcsv($file, [
                $profile->getToken(),
                $profile->getIp(),
                $profile->getMethod(),
                $profile->getUrl(),
                $profile->getTime(),
                $profile->getParentToken(),
                $profile->getStatusCode(),
                $profileSize
            ]);

            fclose($file);
        }

        return true;
    }

    protected function getFilename($token)
    {
        $folderA = substr($token, -2, 2);
        $folderB = substr($token, -4, 2);

        return $this->folder . '/' . $folderA . '/' . $folderB . '/' . $token;
    }

    protected function getIndexFilename()
    {
        return $this->folder . '/index.csv';
    }

    protected function readLineFromFile($file)
    {
        $line     = '';
        $position = ftell($file);

        if (0 === $position) {
            return;
        }

        while (true) {
            $chunkSize = min($position, 1024);
            $position -= $chunkSize;
            fseek($file, $position);

            if (0 === $chunkSize) {
                // bof reached
                break;
            }

            $buffer = fread($file, $chunkSize);

            if (false === ($upTo = strrpos($buffer, "\n"))) {
                $line = $buffer . $line;
                continue;
            }

            $position += $upTo;
            $line = substr($buffer, $upTo + 1) . $line;
            fseek($file, max(0, $position), SEEK_SET);

            if ('' !== $line) {
                break;
            }
        }

        return '' === $line ? null : $line;
    }

    protected function createProfileFromData($token, $data, $parent = null)
    {
        $profile = new Profile($token);
        $profile->setIp($data['ip']);
        $profile->setMethod($data['method']);
        $profile->setUrl($data['url']);
        $profile->setTime($data['time']);
        $profile->setStatusCode($data['status_code']);
        $profile->setCollectors($data['data']);
        $profile->setCollectTime($data['collect_time']);

        if (!$parent && $data['parent']) {
            $parent = $this->read($data['parent']);
        }

        if ($parent) {
            $profile->setParent($parent);
        }

        foreach ($data['children'] as $token) {
            if (!$token || !file_exists($file = $this->getFilename($token))) {
                continue;
            }

            $profile->addChild($this->createProfileFromData($token, unserialize(file_get_contents($file)), $profile));
        }

        return $profile;
    }
}

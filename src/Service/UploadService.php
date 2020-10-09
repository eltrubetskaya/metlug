<?php

namespace App\Service;

use Filestack\Filelink;
use Filestack\FilestackClient;
use Filestack\FilestackException;
use Filestack\FilestackSecurity;
use Psr\Log\LoggerInterface;

class UploadService
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var FilestackSecurity
     */
    private $security;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(string $apiKey, string $apiSecret, LoggerInterface  $logger)
    {
        $this->apiKey = $apiKey;
        $this->security = new FilestackSecurity($apiSecret);
        $this->logger = $logger;
    }

    /**
     * Upload file
     */
    public function upload($filepath): ?string
    {
        try {
            $client = new FilestackClient($this->apiKey, $this->security);

            return $client->upload($filepath)->handle;
        } catch (FilestackException $exception) {
            $this->logger->error(self::class . ': ' . $exception->getMessage());
        }
        return null;
    }

    /**
     * Delete remote file
     */
    public function delete($fileHandle): bool
    {
        try {
            $fileLink = new Filelink($fileHandle, $this->apiKey, $this->security);

            return $fileLink->delete();
        } catch (FilestackException $exception) {
            $this->logger->error(self::class . ': ' . $exception->getMessage());
        }

        return false;
    }
}

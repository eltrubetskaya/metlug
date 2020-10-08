<?php

namespace App\Service;

use Filestack\Filelink;
use Filestack\FilestackClient;
use Filestack\FilestackException;
use Psr\Log\LoggerInterface;

class UploadService
{
    /**
     * @var FilestackClient
     */
    private $client;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(string $apiKey, LoggerInterface  $logger)
    {
        $this->client = new FilestackClient($apiKey);
        $this->logger = $logger;
    }

    public function upload($filepath): ?string
    {
        try {
            return $this->client->upload($filepath)->handle;
        } catch (FilestackException $exception) {
            $this->logger->error(self::class . ': ' . $exception->getMessage());
        }
        return null;
    }
}

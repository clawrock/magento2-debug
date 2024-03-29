<?php
declare(strict_types=1);

namespace ClawRock\Debug\Api\Data;

use ClawRock\Debug\Model\Collector\CollectorInterface;
use ClawRock\Debug\Model\ValueObject\Redirect;

interface ProfileInterface
{
    public function getToken(): string;

    public function setParent(ProfileInterface $parent): void;

    public function getParent(): ?ProfileInterface;

    public function getIp(): string;

    public function getMethod(): string;

    public function getRoute(): string;

    public function getUrl(): string;

    public function getTime(): string;

    public function getStatusCode(): int;

    public function getCollectTime(): float;

    public function getChildren(): array;

    public function getCollector(string $name): CollectorInterface;

    public function getCollectors(): array;

    public function hasCollector(string $name): bool;

    public function getIndex(): array;

    public function getData(): array;

    public function setData(array $data): ProfileInterface;

    public function setFileSize(int $fileSize): ProfileInterface;

    public function getRequestTime(): string;

    public function setRequestTime(string $requestTime): void;

    public function getStatus(): string;

    public function hasRedirect(): bool;

    public function getRedirect(): Redirect;
}

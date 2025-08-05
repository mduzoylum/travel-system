<?php

namespace App\DDD\Modules\Supplier\Domain\ValueObjects;

class ApiCredentials
{
    private array $credentials;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getApiKey(): ?string
    {
        return $this->credentials['api_key'] ?? null;
    }

    public function getApiSecret(): ?string
    {
        return $this->credentials['api_secret'] ?? null;
    }

    public function getUsername(): ?string
    {
        return $this->credentials['username'] ?? null;
    }

    public function getPassword(): ?string
    {
        return $this->credentials['password'] ?? null;
    }

    public function getCustomField(string $field): mixed
    {
        return $this->credentials[$field] ?? null;
    }

    public function toArray(): array
    {
        return $this->credentials;
    }

    public function hasApiKey(): bool
    {
        return !empty($this->getApiKey());
    }

    public function hasBasicAuth(): bool
    {
        return !empty($this->getUsername()) && !empty($this->getPassword());
    }

    public function isValid(): bool
    {
        return $this->hasApiKey() || $this->hasBasicAuth();
    }
} 
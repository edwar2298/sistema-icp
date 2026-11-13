<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{
    public function get(string $key, ?string $default = null): ?string;
    public function set(string $key, ?string $value, string $group = 'general', ?string $description = null): bool;
    public function getAll(): array;
    public function getByGroup(string $group): array;
    public function getNextCorrelative(string $voucherType): array;
    public function incrementCorrelative(string $voucherType): bool;
}


<?php

namespace App\Services\Api\Contracts;

interface ApiServiceInterface {
    public function fetch(string $endpoint): array;
}

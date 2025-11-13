<?php

namespace App\Repositories\Interfaces;

use App\Models\Word;

interface WordRepositoryInterface
{
    public function getRandomWord(): ?Word;
    public function getRandomWordExcluding(array $excludeIds): ?Word;
    public function findById(int $id): ?Word;
    public function search(string $query, int $limit = 20);
    public function all(int $limit = 50);
}

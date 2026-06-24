<?php

namespace App\Infrastructure\Import;

use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerNameMatcher
{
    /** Cocokkan nama secara exact (setelah normalisasi). Null jika tidak ditemukan / ambigu (>1 cocok). */
    public function match(string $name, Collection $customers): ?Customer
    {
        $normalized = $this->normalize($name);

        $matches = $customers->filter(fn (Customer $c) => $this->normalize($c->name) === $normalized);

        return $matches->count() === 1 ? $matches->first() : null;
    }

    /** @return Collection<int, array{customer: Customer, score: float}> Top kandidat mirip, untuk dipilih manual */
    public function suggest(string $name, Collection $customers, int $limit = 3): Collection
    {
        $normalized = $this->normalize($name);

        return $customers
            ->map(function (Customer $c) use ($normalized) {
                similar_text($normalized, $this->normalize($c->name), $percent);

                return ['customer' => $c, 'score' => round($percent, 1)];
            })
            ->filter(fn (array $item) => $item['score'] >= 55)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    public function normalize(string $value): string
    {
        return preg_replace('/\s+/', ' ', mb_strtoupper(trim($value)));
    }
}

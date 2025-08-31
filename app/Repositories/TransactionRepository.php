<?php

namespace App\Repositories;

interface TransactionRepository
{
    public function getDrafts(): array;

    public function isValidSuperior(string $email, int $pin): bool;

    public function saveAsDraft($request): array;

    public function saveAsSales($request, $payment_method_id): array;

    public function voidCart($request): array;

    public function voidItem($request): array;

    public function voidNonDraft($request): array;
}

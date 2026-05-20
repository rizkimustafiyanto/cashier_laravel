<?php

namespace App\Policies;

use App\Models\Transactions;
use App\Models\User;
use App\Enums\TransactionStatus;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transactions $transactions): bool
    {
        return $user->hasRole('cashier');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('cashier');
    }

    /**
     * Determine whether the user can update the model (only if not paid).
     */
    public function update(User $user, Transactions $transactions): bool
    {
        return $user->hasRole('cashier') && is_null($transactions->paid_at) && $transactions->status !==
            TransactionStatus::Paid;
    }

    /**
     * Determine whether the user can delete the model (only if not paid).
     */
    public function delete(User $user, Transactions $transactions): bool
    {
        return $user->hasRole('cashier') && is_null($transactions->paid_at);
    }
}

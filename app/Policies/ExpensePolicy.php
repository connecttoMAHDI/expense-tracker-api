<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function isOwner(User $user, Expense $expense)
    {
        return $user->id === $expense->user->id;
    }
}

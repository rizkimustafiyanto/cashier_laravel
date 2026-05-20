<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetDashboardSummaryAction;
use App\Models\Transactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardSummaryAction $action,
    ) {
    }

    /**
     * Dashboard page
     */

    public function __invoke(): View
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('cashier')) {
            $transactions = Transactions::latest('created_at')->paginate(10);
            return view('dashboard.cashier', compact('transactions'));
        }

        return view('dashboard.marketing', $this->action->execute());
    }
}
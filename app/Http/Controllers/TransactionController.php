<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Transaction\CreateTransactionAction;
use App\Actions\Transaction\DownloadInvoiceAction;
use App\Models\Transactions;
use App\DTOs\CreateTransactionData;
use App\DTOs\TransactionItemData;
use App\Enums\TransactionStatus;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private readonly CreateTransactionAction $action,
    ) {}


    private function authorizeCashier(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        abort_unless($user, 401);

        abort_unless(
            $user->hasRole('cashier'),
            403
        );
    }


    /**
     * Store Transaction
     */

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $items = Collection::make($request->input('items'))->map(fn (array $item) => new TransactionItemData(
            procedureId: $item['procedure_id'],
            procedureName: $item['procedure_name'],
        ))->all();

        $this->action->execute(new CreateTransactionData(
            patientName: $request->validated('patient_name'),
            patientEmail: $request->validated('patient_email'),
            patientPhone: $request->validated('patient_phone'),
            patientGender: $request->validated('patient_gender'),
            patientDob: $request->validated('patient_dob'),
            insuranceId: $request->validated('insurance_id'),
            items: $items,
            cashierId: (string) Auth::id(),
        ));

        return redirect()->back()->with('success', 'Transaction created successfully.');
    }

    public function downloadInvoice(
        Transactions $transaction,
        DownloadInvoiceAction $action,
    ): StreamedResponse {

        return $action->execute(
            $transaction,
        );
    }

    public function pay(Transactions $transaction, Request $request): RedirectResponse
    {
        $this->authorizeCashier();

        if ($transaction->paid_at) {
            return redirect()->back()->with('error', 'Transaction already paid.');
        }

        $transaction->update([
            'paid_at' => now(),
            'status' => TransactionStatus::Paid->value,
        ]);

        return redirect()->back()->with('success', 'Transaction marked as paid.');
    }

    public function destroy(Transactions $transaction): RedirectResponse
    {
        $this->authorizeCashier();

        if ($transaction->paid_at) {
            return redirect()->back()->with('error', 'Paid transactions cannot be deleted.');
        }

        $transaction->items()->delete();
        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }

}

<?php

declare(strict_types=1);

namespace App\Livewire\Transactions;

use App\Actions\Transaction\CreateTransactionAction;
use App\Actions\Transaction\UpdateTransactionAction;
use App\DTOs\CreateTransactionData;
use App\DTOs\TransactionItemData;
use App\DTOs\UpdateTransactionData;
use App\Enums\VoucherTypes;
use App\Models\Transactions;
use App\Models\Voucher;
use App\Services\API\RecruitmentApiService;
use App\Services\Voucher\VoucherCalculationService;
use Flux\Flux;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Component;
use Throwable;

class CreateTransaction extends Component
{
    public string $patientName = '';
    public string $patientEmail = '';
    public string $patientPhone = '';
    public string $patientGender = '';
    public ?string $patientDob = null;
    public ?string $insuranceId = null;
    public array $items = [];
    public array $insurance = [];
    public array $procedures = [];
    public float $subtotal = 0;
    public float $discountTotal = 0;
    public float $grandTotal = 0;
    public ?array $voucherSummary = null;
    public ?Transactions $transaction = null;

    public function mount(?Transactions $transaction = null): void
    {
        $this->loadLookupData();

        if ($transaction !== null) {
            if ($transaction->paid_at) {
                abort(403, 'Paid transaction cannot be edited.');
            }

            $this->transaction = $transaction;
            $this->patientName = $transaction->patient_name;
            $this->patientEmail = $transaction->patient_email ?? '';
            $this->patientPhone = $transaction->patient_phone ?? '';
            $this->patientGender = $transaction->patient_gender ?? '';
            $this->patientDob = $transaction->patient_dob ? (
                is_string($transaction->patient_dob)
                    ? $transaction->patient_dob
                    : $transaction->patient_dob->format('Y-m-d')
            ) : null;
            $this->insuranceId = $transaction->insurance_id;
            $this->items = $transaction->items->map(fn ($item) => [
                'procedure_id' => $item->procedure_id,
                'procedure_name' => $item->procedure_name,
                'price' => (float) $item->base_price,
                'discount' => (float) $item->discount_amount,
                'final_price' => (float) $item->final_price,
            ])->all();
        } else {
            $this->addItem();
        }

        $this->recalculateTotals();
    }

    protected function rules(): array
    {
        return [
            'patientName' => ['required', 'string', 'max:255'],
            'patientEmail' => ['nullable', 'email', 'max:255'],
            'patientPhone' => ['nullable', 'string', 'max:20'],
            'patientGender' => ['nullable', 'in:male,female,other'],
            'patientDob' => ['nullable', 'date'],
            'insuranceId' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.procedure_id' => ['required', 'string'],
            'items.*.procedure_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function addItem(): void
    {
        $this->items[] = [
            'procedure_id' => '',
            'procedure_name' => '',
            'price' => 0,
            'discount' => 0,
            'final_price' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if (count($this->items) === 0) {
            $this->addItem();
        }

        $this->recalculateTotals();
    }

    public function updatedItemsProcedureId(mixed $value, string $key): void
    {
        if (! str_contains($key, 'procedure_id')) {
            return;
        }

        $index = (int) explode('.', $key)[1];

        $procedure = collect($this->procedures)->firstWhere('id', $value);

        $this->items[$index]['procedure_name'] = $procedure['name'] ?? '';
        
        $this->recalculateTotals();
    }

    public function updatedInsuranceId(): void
    {
        $this->recalculateTotals();
    }

    private function loadLookupData(): void
    {
        try {
            $apiService = app(RecruitmentApiService::class);

            $this->insurance = collect($apiService->getInsurance())
                ->sortBy('name')
                ->values()
                ->all();

            $this->procedures = collect($apiService->getProcedures())
                ->sortBy('name')
                ->values()
                ->all();
        } catch (Throwable) {
            $this->insurance = [];
            $this->procedures = [];
        }
    }

    private function resolveVoucherForInsurance(): ?Voucher
    {
        if (empty($this->insuranceId)) {
            $this->setVoucherSummary(null);
            return null;
        }

        $insurance = collect($this->insurance)->firstWhere('id', $this->insuranceId);
        $insuranceName = data_get($insurance, 'name');

        $voucher = Voucher::query()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query) use ($insuranceName) {
                $query->where('insurance_id', $this->insuranceId);

                if ($insuranceName) {
                    $query->orWhere('insurance_name', $insuranceName);
                }
            })
            ->first();

        $this->setVoucherSummary($voucher);

        return $voucher;
    }

    private function setVoucherSummary(?Voucher $voucher): void
    {
        if ($voucher === null) {
            $this->voucherSummary = null;
            return;
        }

        $this->voucherSummary = [
            'label' => $this->formatVoucherLabel($voucher),
            'insurance_name' => $voucher->insurance_name,
            'fallback' => ! $voucher->exists,
        ];
    }

    private function formatVoucherLabel(Voucher $voucher): string
    {
        return match ($voucher->type) {
            VoucherTypes::Percentage->value => __(':percent% off up to :max', [
                'percent' => number_format((float) $voucher->value, 0),
                'max' => number_format((float) $voucher->max_discount, 0, ',', '.'),
            ]),
            default => __('Rp :amount off', [
                'amount' => number_format((float) $voucher->value, 0, ',', '.'),
            ]),
        };
    }

    private function resolveProcedurePrice(string $procedureId): float
    {
        try {
            $priceData = app(RecruitmentApiService::class)->getProcedurePrices($procedureId);

            return (float) (
                $priceData['price'] ??
                data_get($priceData, 'unit_price') ??
                0
            );
        } catch (ConnectionException) {
            return 0;
        }
    }

    public function recalculateTotals(): void
    {
        $voucher = $this->resolveVoucherForInsurance();

        $subtotal = 0;
        $discountTotal = 0;

        foreach ($this->items as $index => $item) {
            if (empty($item['procedure_id'])) {
                $this->items[$index]['price'] = 0;
                $this->items[$index]['discount'] = 0;
                $this->items[$index]['final_price'] = 0;
                continue;
            }

            $price = $this->resolveProcedurePrice($item['procedure_id']);
            $result = app(VoucherCalculationService::class)->calculate($price, $voucher);

            $this->items[$index]['price'] = $result->basePrice;
            $this->items[$index]['discount'] = $result->discountAmount;
            $this->items[$index]['final_price'] = $result->finalPrice;

            $subtotal += $result->basePrice;
            $discountTotal += $result->discountAmount;
        }

        $this->subtotal = round($subtotal, 2);
        $this->discountTotal = round($discountTotal, 2);
        $this->grandTotal = round($subtotal - $discountTotal, 2);
    }

    private function resetTransactionForm(): void
    {
        $this->patientName = '';
        $this->patientEmail = '';
        $this->patientPhone = '';
        $this->patientGender = '';
        $this->patientDob = null;
        $this->items = [];
        $this->addItem();
        $this->recalculateTotals();
    }

    public function save(CreateTransactionAction $createTransactionAction, UpdateTransactionAction $updateTransactionAction): void
    {
        // Ensure procedure_name is populated for each selected item (in case client didn't set it)
        foreach ($this->items as $idx => $itm) {
            if (! empty($itm['procedure_id']) && empty($itm['procedure_name'])) {
                $proc = collect($this->procedures)->firstWhere('id', $itm['procedure_id']);
                $this->items[$idx]['procedure_name'] = $proc['name'] ?? '';
            }
        }

        $this->recalculateTotals();

        $this->validate();

        $items = Collection::make($this->items)
            ->filter(fn (array $item) => filled($item['procedure_id']))
            ->map(fn (array $item) => new TransactionItemData(
                procedureId: $item['procedure_id'],
                procedureName: $item['procedure_name'],
            ))
            ->all();

        if (count($items) === 0) {
            $this->addError('items', __('Select at least one procedure to continue.'));

            return;
        }

        try {
            if ($this->transaction !== null) {
                $transaction = $updateTransactionAction->execute(
                    $this->transaction,
                    new UpdateTransactionData(
                        patientName: $this->patientName,
                        patientEmail: $this->patientEmail,
                        patientPhone: $this->patientPhone,
                        patientGender: $this->patientGender,
                        patientDob: $this->patientDob,
                        insuranceId: $this->insuranceId,
                        items: $items,
                    )
                );

                $message = __('Transaction :invoice updated successfully.', [
                    'invoice' => $transaction->invoice_number,
                ]);
            } else {
                $transaction = $createTransactionAction->execute(
                    new CreateTransactionData(
                        patientName: $this->patientName,
                        patientEmail: $this->patientEmail,
                        patientPhone: $this->patientPhone,
                        patientGender: $this->patientGender,
                        patientDob: $this->patientDob,
                        insuranceId: $this->insuranceId,
                        items: $items,
                        cashierId: (string) Auth::id(),
                    )
                );

                $message = __('Transaction :invoice created successfully.', [
                    'invoice' => $transaction->invoice_number,
                ]);
            }

            session()->flash('success', $message);
            Flux::toast(variant: 'success', text: $message);

            if ($this->transaction === null) {
                $this->resetTransactionForm();
            }

            $this->redirect(route('dashboard'));
        } catch (\Throwable $e) {
            logger()->error('Failed to save transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->addError('save', __('Unable to save transaction right now. Please try again.'));
            session()->flash('error', __('Unable to save transaction: :message', ['message' => $e->getMessage()]));
            Flux::toast(variant: 'danger', text: __('Unable to save transaction.'));
        }
    }

    public function render()
    {
        return view('livewire.transactions.create-transactions');
    }

    public function getIsReadyProperty(): bool
    {
        $patientFieldsFilled =
            filled($this->patientName) &&
            filled($this->patientEmail) &&
            filled($this->patientPhone) &&
            filled($this->patientGender) &&
            filled($this->patientDob);

        $itemsValid = collect($this->items)
            ->every(fn ($item) => filled(
                data_get($item, 'procedure_id')
            ));

        return
            $patientFieldsFilled &&
            $itemsValid &&
            count($this->items) > 0;
    }
}

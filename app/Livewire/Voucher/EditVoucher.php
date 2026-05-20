<?php

declare(strict_types=1);

namespace App\Livewire\Voucher;

use App\Actions\Voucher\UpdateVoucherAction;
use App\Models\Voucher;
use App\Services\API\RecruitmentApiService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EditVoucher extends Component
{
    use Authorizable;

    public Voucher $voucher;

    public string $insuranceId = '';
    public string $insuranceName = '';
    public string $type = 'percentage';
    public string $value = '';
    public ?string $maxDiscount = '';
    public ?string $startDate = '';
    public ?string $endDate = '';
    public bool $isActive = true;

    public array $insuranceOptions = [];

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);
        $this->checkAuthorization();
        $this->loadData();
        $this->loadInsurances();
    }

    protected function rules(): array
    {
        return [
            'insuranceId' => ['required', 'string', 'max:255'],
            'insuranceName' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'maxDiscount' => ['nullable', 'numeric', 'min:0'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'isActive' => ['boolean'],
        ];
    }

    public function updatedInsuranceId(): void
    {
        $insurance = collect($this->insuranceOptions)->firstWhere('id', $this->insuranceId);
        if ($insurance) {
            $this->insuranceName = $insurance['name'] ?? '';
        }
    }

    public function update(): void
    {
        $this->checkAuthorization();
        
        $validated = $this->validate();
        
        try {
            (new UpdateVoucherAction())($this->voucher, array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

            Flux::toast(text: 'Voucher updated successfully', variant: 'success');
            $this->redirect(route('vouchers.index'), navigate: true);
        } catch (\Throwable $e) {
            Flux::toast(text: 'Failed to update voucher: ' . $e->getMessage(), variant: 'error');
        }
    }

    private function checkAuthorization(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        abort_unless($user, 401);

        abort_unless(
            $user->hasRole('marketing'),
            403
        );
    }

    private function loadData(): void
    {
        $this->insuranceId = $this->voucher->insurance_id;
        $this->insuranceName = $this->voucher->insurance_name;
        $this->type = $this->voucher->type;
        $this->value = (string) $this->voucher->value;
        $this->maxDiscount = $this->voucher->max_discount ? (string) $this->voucher->max_discount : null;
        $this->startDate = $this->voucher->start_date->format('Y-m-d');
        $this->endDate = $this->voucher->end_date->format('Y-m-d');
        $this->isActive = $this->voucher->is_active;
    }

    private function loadInsurances(): void
    {
        $this->insuranceOptions = collect(
            app(RecruitmentApiService::class)->getInsurance()
        )
            ->sortBy('name')
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.voucher.edit-voucher');
    }
}

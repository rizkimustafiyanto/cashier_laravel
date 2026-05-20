<?php

declare(strict_types=1);

namespace App\Livewire\Voucher;

use App\Actions\Voucher\CreateVoucherAction;
use App\Services\API\RecruitmentApiService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CreateVoucher extends Component
{
    use Authorizable;

    public string $insuranceId = '';
    public string $insuranceName = '';
    public string $type = 'percentage';
    public string $value = '';
    public ?string $maxDiscount = '';
    public ?string $startDate = '';
    public ?string $endDate = '';
    public bool $isActive = true;

    /**
     * @var array<int, array{id: string, name: string}>
     */
    public array $insuranceOptions = [];

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);

        $this->checkAuthorization();

        $this->loadInsurances();

        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addMonth()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'insuranceId' => [
                'required',
                'string',
                'max:255',
            ],

            'insuranceName' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'in:percentage,fixed',
            ],

            'value' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'maxDiscount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'startDate' => [
                'required',
                'date',
            ],

            'endDate' => [
                'required',
                'date',
                'after_or_equal:startDate',
            ],

            'isActive' => [
                'boolean',
            ],
        ];
    }

    public function updatedInsuranceId(): void
    {
        $insurance = collect($this->insuranceOptions)
            ->firstWhere('id', $this->insuranceId);

        if ($insurance) {
            $this->insuranceName = $insurance['name'];
        }
    }

    public function create(): void
    {
        $this->checkAuthorization();

        $validated = $this->validate();

        try {
            (new CreateVoucherAction())(
                array_merge($validated, [
                    'created_by' => Auth::id(),
                ])
            );

            Flux::toast(
                text: 'Voucher created successfully',
                variant: 'success'
            );

            $this->redirect(
                route('vouchers.index'),
                navigate: true
            );
        } catch (\Throwable $e) {
            report($e);

            Flux::toast(
                text: 'Failed to create voucher',
                variant: 'danger'
            );
        }
    }

    private function checkAuthorization(
        ?string $permission = null
    ): void {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        abort_unless($user, 401);

        if ($permission) {
            abort_unless(
                $user->can($permission),
                403
            );

            return;
        }

        abort_unless(
            $user->hasRole('marketing'),
            403
        );
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
        return view('livewire.voucher.create-voucher');
    }
}
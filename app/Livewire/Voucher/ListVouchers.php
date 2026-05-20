<?php

declare(strict_types=1);

namespace App\Livewire\Voucher;

use App\Models\Voucher;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListVouchers extends Component
{
    use WithPagination;


    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortOrder = 'desc';
    public string $filterStatus = 'all';

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);
        $this->checkAuthorization();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus']);
        $this->sortBy = 'created_at';
        $this->sortOrder = 'desc';
        $this->resetPage();
    }

    public function toggleSort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }
    }

    public function deleteVoucher(Voucher $voucher): void
    {
        $this->checkAuthorization();
        $voucher->delete();
        Flux::toast(text: 'Voucher deleted successfully', variant: 'success');
    }

    public function toggleActive(Voucher $voucher): void
    {
        $this->checkAuthorization();
        $voucher->update(['is_active' => !$voucher->is_active]);
        Flux::toast(
            text: $voucher->is_active ? 'Voucher activated' : 'Voucher deactivated',
            variant: 'success'
        );
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

    #[Computed]
    public function vouchers(): LengthAwarePaginator
    {
        return Voucher::query()
            ->when($this->search, function (Builder $query) {
                $query->where('insurance_name', 'like', "%{$this->search}%")
                    ->orWhere('insurance_id', 'like', "%{$this->search}%");
            })
            ->when($this->filterStatus !== 'all', function (Builder $query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->orderBy($this->sortBy, $this->sortOrder)
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.voucher.list-vouchers', [
            'vouchers' => $this->vouchers,
        ]);
    }
}

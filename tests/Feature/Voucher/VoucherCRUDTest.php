<?php

namespace Tests\Feature\Voucher;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class VoucherCRUDTest extends TestCase
{
    use RefreshDatabase;

    private User $marketingUser;
    private User $cashierUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marketingUser = User::factory()->create();
        $this->marketingUser->assignRole('marketing');

        $this->cashierUser = User::factory()->create();
        $this->cashierUser->assignRole('cashier');
    }

    /**
     * Test marketing user can view voucher list
     */
    public function test_marketing_user_can_view_voucher_list(): void
    {
        $this->actingAs($this->marketingUser);

        $response = $this->get(route('vouchers.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('voucher.list-vouchers');
    }

    /**
     * Test cashier user cannot view voucher list
     */
    public function test_cashier_user_cannot_view_voucher_list(): void
    {
        $this->actingAs($this->cashierUser);

        $response = $this->get(route('vouchers.index'));

        $response->assertStatus(403);
    }

    /**
     * Test marketing user can access create voucher page
     */
    public function test_marketing_user_can_access_create_page(): void
    {
        $this->actingAs($this->marketingUser);

        $response = $this->get(route('vouchers.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('voucher.create-voucher');
    }

    /**
     * Test marketing user can create percentage voucher
     */
    public function test_can_create_percentage_voucher(): void
    {
        $this->actingAs($this->marketingUser);

        $data = [
            'insuranceId' => 'bpjs',
            'insuranceName' => 'BPJS Kesehatan',
            'type' => 'percentage',
            'value' => 10,
            'maxDiscount' => 500000,
            'startDate' => now()->format('Y-m-d'),
            'endDate' => now()->addMonth()->format('Y-m-d'),
            'isActive' => true,
        ];

        Livewire::test('voucher.create-voucher')
            ->set($data)
            ->call('create')
            ->assertRedirect(route('vouchers.index'));

        $this->assertDatabaseHas('vouchers', [
            'insurance_id' => 'bpjs',
            'type' => 'percentage',
            'value' => 10.00,
        ]);
    }

    /**
     * Test can create fixed amount voucher
     */
    public function test_can_create_fixed_amount_voucher(): void
    {
        $this->actingAs($this->marketingUser);

        $data = [
            'insuranceId' => 'asuransi_xyz',
            'insuranceName' => 'Asuransi XYZ',
            'type' => 'fixed',
            'value' => 50000,
            'startDate' => now()->format('Y-m-d'),
            'endDate' => now()->addMonth()->format('Y-m-d'),
            'isActive' => true,
        ];

        Livewire::test('voucher.create-voucher')
            ->set($data)
            ->call('create')
            ->assertRedirect(route('vouchers.index'));

        $this->assertDatabaseHas('vouchers', [
            'insurance_id' => 'asuransi_xyz',
            'type' => 'fixed',
            'value' => 50000.00,
        ]);
    }

    /**
     * Test validation on create voucher
     */
    public function test_validation_on_create_voucher(): void
    {
        $this->actingAs($this->marketingUser);

        Livewire::test('voucher.create-voucher')
            ->set('insuranceId', '')
            ->set('type', 'invalid_type')
            ->set('value', -100)
            ->set('endDate', now()->subDay()->format('Y-m-d'))
            ->call('create')
            ->assertHasErrors(['insuranceId', 'type', 'value', 'endDate']);
    }

    /**
     * Test marketing user can update voucher
     */
    public function test_can_update_voucher(): void
    {
        $voucher = Voucher::factory()->create([
            'type' => 'percentage',
            'value' => 5,
        ]);

        $this->actingAs($this->marketingUser);

        Livewire::test('voucher.edit-voucher', ['voucher' => $voucher])
            ->set('value', '15')
            ->call('update')
            ->assertRedirect(route('vouchers.index'));

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'value' => 15.00,
        ]);
    }

    /**
     * Test marketing user can delete voucher
     */
    public function test_can_delete_voucher(): void
    {
        $voucher = Voucher::factory()->create();

        $this->actingAs($this->marketingUser);

        Livewire::test('voucher.list-vouchers')
            ->call('deleteVoucher', $voucher->id);

        $this->assertSoftDeleted('vouchers', ['id' => $voucher->id]);
    }

    /**
     * Test can toggle voucher active status
     */
    public function test_can_toggle_voucher_active_status(): void
    {
        $voucher = Voucher::factory()->create(['is_active' => true]);

        $this->actingAs($this->marketingUser);

        Livewire::test('voucher.list-vouchers')
            ->call('toggleActive', $voucher->id);

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test unauthorized access to edit voucher
     */
    public function test_cashier_cannot_edit_voucher(): void
    {
        $voucher = Voucher::factory()->create();

        $this->actingAs($this->cashierUser);

        $response = $this->get(route('vouchers.edit', $voucher));

        $response->assertStatus(403);
    }
}

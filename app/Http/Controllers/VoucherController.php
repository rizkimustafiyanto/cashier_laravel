<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    private function authorizeMarketing(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        abort_unless($user, 401);

        abort_unless(
            $user->hasRole('marketing'),
            403
        );
    }

    /**
     * Delete a voucher (soft delete)
     */
    public function destroy(Voucher $voucher): JsonResponse
    {
        $this->authorizeMarketing();

        $voucher->delete();

        return response()->json([
            'message' => 'Voucher deleted successfully',
            'data' => $voucher,
        ], 200);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Voucher $voucher): JsonResponse
    {
        $this->authorizeMarketing();

        $voucher->update([
            'is_active' => !$voucher->is_active,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Voucher status updated',
            'data' => $voucher,
        ], 200);
    }

    /**
     * Restore a soft-deleted voucher
     */
    public function restore(string $id): JsonResponse
    {
        $voucher = Voucher::withTrashed()->findOrFail($id);

        $this->authorizeMarketing();

        $voucher->restore();

        return response()->json([
            'message' => 'Voucher restored successfully',
            'data' => $voucher,
        ], 200);
    }

    /**
     * Permanently delete a voucher
     */
    public function forceDelete(string $id): JsonResponse
    {
        $voucher = Voucher::withTrashed()->findOrFail($id);

        $this->authorizeMarketing();

        $voucher->forceDelete();

        return response()->json([
            'message' => 'Voucher permanently deleted',
        ], 200);
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Voucher;

use App\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Voucher $voucher,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vouchers'),
        ];
    }
}

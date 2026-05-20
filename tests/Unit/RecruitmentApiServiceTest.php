<?php

declare(strict_types=1);

use App\Services\API\RecruitmentApiService;
use Illuminate\Support\Facades\Http;

it('fetches insurances', function () {

    Http::fake([
        '*/insurances' => Http::response([
            'data' => [
                [
                    'id' => '1',
                    'name' => 'BPJS',
                ],
            ],
        ]),
        '*' => Http::response([
            'access_token' => 'test-token',
        ]),

    ]);

    $result = app(
        RecruitmentApiService::class
    )->getInsurance();

    expect(
        $result
    )->toHaveCount(1);

    expect(
        $result[0]['name']
    )->toBe('BPJS');
});

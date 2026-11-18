<?php

namespace Tests\Feature;

use App\Exceptions\ApiException;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class ReportTest extends TestCase
{
    private function adminRequest(array $parameters = []): Request
    {
        $request = Request::create(
            '/api/v1/reports/most-loaned',
            'GET',
            $parameters
        );

        $request->attributes->set('promoter', (object) [
            'id' => 'admin-1',
            'role' => 'admin',
        ]);

        return $request;
    }

    public function test_report_requires_authentication(): void
    {
        $response = $this->getJson(
            '/api/v1/reports/most-loaned'
        );

        $response->assertStatus(401);
    }

    public function test_report_rejects_missing_authenticated_user(): void
    {
        $request = Request::create(
            '/api/v1/reports/most-loaned',
            'GET'
        );

        $this->expectException(ApiException::class);

        (new ReportController())->mostLoaned($request);
    }

    public function test_report_rejects_promoter_role(): void
    {
        $request = Request::create(
            '/api/v1/reports/most-loaned',
            'GET'
        );

        $request->attributes->set('promoter', (object) [
            'id' => 'promoter-1',
            'role' => 'promoter',
        ]);

        $this->expectException(ApiException::class);

        (new ReportController())->mostLoaned($request);
    }

    public function test_report_rejects_zero_limit(): void
    {
        $request = $this->adminRequest([
            'limit' => 0,
        ]);

        $this->expectException(ValidationException::class);

        (new ReportController())->mostLoaned($request);
    }

    public function test_report_rejects_limit_above_one_hundred(): void
    {
        $request = $this->adminRequest([
            'limit' => 101,
        ]);

        $this->expectException(ValidationException::class);

        (new ReportController())->mostLoaned($request);
    }

    public function test_report_rejects_non_numeric_limit(): void
    {
        $request = $this->adminRequest([
            'limit' => 'invalid',
        ]);

        $this->expectException(ValidationException::class);

        (new ReportController())->mostLoaned($request);
    }

    public function test_report_rejects_negative_limit(): void
    {
        $request = $this->adminRequest([
            'limit' => -5,
        ]);

        $this->expectException(ValidationException::class);

        (new ReportController())->mostLoaned($request);
    }
}

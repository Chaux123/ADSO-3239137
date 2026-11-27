<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ArchitectureTest extends TestCase
{
    public function test_domain_does_not_depend_on_laravel_or_adapters(): void
    {
        foreach (glob(__DIR__ . '/../../app/Domain/*.php') as $file) {
            $source = file_get_contents($file);
            $this->assertDoesNotMatchRegularExpression('/(?:Illuminate|App\\\\Adapters|App\\\\Http)\\\\/', $source, basename($file));
        }
    }
    public function test_catalog_family_and_promoter_controllers_do_not_query_database_directly(): void
    {
        foreach (['ToyController.php', 'FamilyController.php', 'PromoterController.php', 'AuthController.php', 'LoanController.php', 'ReportController.php'] as $name) {
            $source = file_get_contents(__DIR__ . '/../../app/Http/Controllers/' . $name);
            $this->assertStringNotContainsString('DB::', $source, $name);
        }
    }

    public function test_family_registration_depends_on_port_not_database_adapter(): void
    {
        $source = file_get_contents(__DIR__ . '/../../app/Application/RegisterFamily.php');
        $this->assertStringContainsString('FamilyPort', $source);
        $this->assertStringNotContainsString('App\\Adapters', $source);
        $this->assertStringNotContainsString('DB::', $source);
    }

    public function test_authentication_and_promoter_registration_depend_on_ports(): void
    {
        foreach (['Authenticate.php', 'RegisterPromoter.php'] as $name) {
            $source = file_get_contents(__DIR__ . '/../../app/Application/' . $name);
            $this->assertStringContainsString('PromoterPort', $source);
            $this->assertStringNotContainsString('App\\Adapters', $source);
            $this->assertStringNotContainsString('DB::', $source);
        }
    }
}

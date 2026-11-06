<?php

namespace Tests\Unit\Services;

use App\Models\Farmer;
use App\Models\LatexTransaction;
use App\Models\Plot;
use App\Services\YieldForecastService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class YieldForecastServiceTest extends TestCase
{
    use RefreshDatabase;

    protected YieldForecastService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new YieldForecastService();
    }

    #[Test]
    public function it_calculates_forecast_correctly_using_historical_transactions(): void
    {
        $farmer = Farmer::factory()->create();

        $plot = Plot::factory()->create([
            'farmer_id' => $farmer->id,
            'plot_size_rai' => 2.0,
        ]);

        LatexTransaction::factory()->create([
            'plot_id' => $plot->id,
            'volume_kg' => 200,
            'dry_rubber_content' => 30,
            'dry_rubber_weight_kg' => 60,
        ]);

        LatexTransaction::factory()->create([
            'plot_id' => $plot->id,
            'volume_kg' => 150,
            'dry_rubber_content' => 30,
            'dry_rubber_weight_kg' => 40,
        ]);

        $result = $this->service->calculateForecast(
            plotId: $plot->id,
            targetHarvestDate: '2026-05-15',
            scheduledTappingDays: 10,
            targetRevenueGoal: 20000.00,
            estimatedDrc: 32.0,
            targetPricePerKg: 50.00
        );

        $this->assertArrayHasKey('projected_raw_latex_kg', $result);
        $this->assertArrayHasKey('projected_dry_rubber_kg', $result);
        $this->assertArrayHasKey('projected_revenue', $result);
        $this->assertArrayHasKey('completion_rate', $result);
        $this->assertArrayHasKey('advisory_status', $result);
        $this->assertArrayHasKey('advisory_message', $result);

        $this->assertEquals(500.0, $result['projected_dry_rubber_kg']);
        $this->assertEquals(25000.0, $result['projected_revenue']);
        $this->assertEquals(125.0, $result['completion_rate']);
        $this->assertEquals('optimal', $result['advisory_status']);
    }

    #[Test]
    #[DataProvider('highSeasonMonthsProvider')]
    public function it_applies_high_yield_peak_season_multiplier(int $month): void
    {
        $plot = Plot::factory()->create(['plot_size_rai' => 1.0]);
        $date = sprintf('2026-%02d-10', $month);

        $result = $this->service->calculateForecast(
            plotId: $plot->id,
            targetHarvestDate: $date,
            scheduledTappingDays: 1,
            targetRevenueGoal: 1000.00,
            estimatedDrc: 32.0,
            targetPricePerKg: 50.00
        );

        $this->assertEquals(937.5, $result['projected_revenue']);
    }

    public static function highSeasonMonthsProvider(): array
    {
        return [[10], [11], [12], [1]];
    }

    #[Test]
    #[DataProvider('lowSeasonMonthsProvider')]
    public function it_applies_low_yield_defoliation_season_multiplier(int $month): void
    {
        $plot = Plot::factory()->create(['plot_size_rai' => 1.0]);
        $date = sprintf('2026-%02d-10', $month);

        $result = $this->service->calculateForecast(
            plotId: $plot->id,
            targetHarvestDate: $date,
            scheduledTappingDays: 1,
            targetRevenueGoal: 1000.00,
            estimatedDrc: 32.0,
            targetPricePerKg: 50.00
        );

        $this->assertEquals(562.5, $result['projected_revenue']);
    }

    public static function lowSeasonMonthsProvider(): array
    {
        return [[2], [3], [4]];
    }

    #[Test]
    public function it_returns_warning_advisory_status_when_revenue_goal_is_not_met(): void
    {
        $plot = Plot::factory()->create(['plot_size_rai' => 1.0]);

        $result = $this->service->calculateForecast(
            plotId: $plot->id,
            targetHarvestDate: '2026-06-01',
            scheduledTappingDays: 2,
            targetRevenueGoal: 50000.00,
            estimatedDrc: 30.0,
            targetPricePerKg: 40.00
        );

        $this->assertLessThan(100.0, $result['completion_rate']);
        $this->assertEquals('warning', $result['advisory_status']);
    }

    #[Test]
    public function it_handles_zero_revenue_target_without_dividing_by_zero(): void
    {
        $plot = Plot::factory()->create(['plot_size_rai' => 1.0]);

        $result = $this->service->calculateForecast(
            plotId: $plot->id,
            targetHarvestDate: '2026-06-01',
            scheduledTappingDays: 5,
            targetRevenueGoal: 0.0,
            estimatedDrc: 30.0,
            targetPricePerKg: 50.00
        );

        $this->assertEquals(100.0, $result['completion_rate']);
        $this->assertEquals('optimal', $result['advisory_status']);
    }
}
<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health;

use EoneoPay\Externals\DataTransferObjects\Health\HealthExtendedCheckResult;
use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Health;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Tests\EoneoPay\Externals\Stubs\Health\HealthCheckStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Health\Health
 */
class HealthTest extends TestCase
{
    /**
     * Gets the health check scenarios for testing.
     *
     * @return mixed[]
     */
    public function getExtendedCheckScenarios(): iterable
    {
        yield 'All services healthy' => [
            'checks' => [
                new HealthCheckStub(
                    'Test Health Check',
                    'test-health-check',
                    new HealthState(HealthInterface::STATE_HEALTHY)
                )
            ],
            'expected' => new HealthExtendedCheckResult(
                HealthInterface::STATE_HEALTHY,
                [
                    'test-health-check' => new HealthState(HealthInterface::STATE_HEALTHY),
                ]
            ),
        ];

        yield 'All services degraded' => [
            'checks' => [
                new HealthCheckStub(
                    'Test Health Check',
                    'test-health-check',
                    new HealthState(HealthInterface::STATE_DEGRADED)
                )
            ],
            'expected' => new HealthExtendedCheckResult(
                HealthInterface::STATE_DEGRADED,
                [
                    'test-health-check' => new HealthState(HealthInterface::STATE_DEGRADED),
                ]
            ),
        ];

        yield 'At least one service degraded' => [
            'checks' => [
                new HealthCheckStub(
                    'Test Service One',
                    'test-service-one',
                    new HealthState(HealthInterface::STATE_HEALTHY)
                ),
                new HealthCheckStub(
                    'Test Service Two',
                    'test-service-two',
                    new HealthState(HealthInterface::STATE_DEGRADED)
                ),
            ],
            'expected' => new HealthExtendedCheckResult(
                HealthInterface::STATE_DEGRADED,
                [
                    'test-service-one' => new HealthState(HealthInterface::STATE_HEALTHY),
                    'test-service-two' => new HealthState(HealthInterface::STATE_DEGRADED),
                ]
            ),
        ];
    }

    /**
     * Tests that the extended health check returns a healthy result when no checks have
     * been passed via dependency injection.
     *
     * @return void
     */
    public function testExtendedCheckReturnsEmptyResultWhenNoChecksPassed(): void
    {
        $instance = $this->getInstance([]);
        $expected = new HealthExtendedCheckResult(
            HealthInterface::STATE_HEALTHY,
            []
        );

        $result = $instance->extended();

        self::assertEquals($expected, $result);
    }

    /**
     * Tests that the 'extended' method returns the expected result for each scenario provided by the data provider.
     *
     * @param \EoneoPay\Externals\Health\Interfaces\HealthCheckInterface[] $checks
     * @param \EoneoPay\Externals\DataTransferObjects\Health\HealthExtendedCheckResult $expected
     *
     * @return void
     *
     * @dataProvider getExtendedCheckScenarios
     */
    public function testExtendedCheckReturnsExpectedScenarioResults(
        array $checks,
        HealthExtendedCheckResult $expected
    ): void {
        $instance = $this->getInstance($checks);

        $result = $instance->extended();

        self::assertEquals($expected, $result);
    }

    /**
     * Tests that the combined checker's 'simple' method returns a positive value.
     *
     * @return void
     */
    public function testSimpleCheckReturnsPositiveResult(): void
    {
        $instance = $this->getInstance([]);

        $result = $instance->simple();

        self::assertSame(HealthInterface::STATE_HEALTHY, $result);
    }

    /**
     * Gets an instance of the health service.
     *
     * @param \EoneoPay\Externals\Health\Interfaces\HealthCheckInterface[] $checks
     *
     * @return \EoneoPay\Externals\Health\Health
     */
    private function getInstance(array $checks): Health
    {
        return new Health($checks);
    }
}

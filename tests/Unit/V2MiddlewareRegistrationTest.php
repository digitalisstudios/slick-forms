<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Http\Middleware\CheckFormSchedule;
use DigitalisStudios\SlickForms\Http\Middleware\CheckIpRestrictions;
use DigitalisStudios\SlickForms\Http\Middleware\CheckSubmissionLimits;
use DigitalisStudios\SlickForms\Http\Middleware\VerifyFormPassword;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test V2 middleware are registered correctly
 */
class V2MiddlewareRegistrationTest extends TestCase
{
    /** @test */
    public function form_password_middleware_is_registered()
    {
        $router = app('router');
        $middleware = $router->getMiddleware();

        $this->assertArrayHasKey('slick-forms.password', $middleware);
        $this->assertEquals(VerifyFormPassword::class, $middleware['slick-forms.password']);
    }

    /** @test */
    public function form_schedule_middleware_is_registered()
    {
        $router = app('router');
        $middleware = $router->getMiddleware();

        $this->assertArrayHasKey('slick-forms.schedule', $middleware);
        $this->assertEquals(CheckFormSchedule::class, $middleware['slick-forms.schedule']);
    }

    /** @test */
    public function submission_limits_middleware_is_registered()
    {
        $router = app('router');
        $middleware = $router->getMiddleware();

        $this->assertArrayHasKey('slick-forms.limits', $middleware);
        $this->assertEquals(CheckSubmissionLimits::class, $middleware['slick-forms.limits']);
    }

    /** @test */
    public function ip_restrictions_middleware_is_registered()
    {
        $router = app('router');
        $middleware = $router->getMiddleware();

        $this->assertArrayHasKey('slick-forms.ip', $middleware);
        $this->assertEquals(CheckIpRestrictions::class, $middleware['slick-forms.ip']);
    }

    /** @test */
    public function all_middleware_can_be_instantiated()
    {
        $middlewares = [
            VerifyFormPassword::class,
            CheckFormSchedule::class,
            CheckSubmissionLimits::class,
            CheckIpRestrictions::class,
        ];

        foreach ($middlewares as $middlewareClass) {
            $middleware = app($middlewareClass);
            $this->assertInstanceOf($middlewareClass, $middleware);
        }
    }
}

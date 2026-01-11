<?php

namespace Modules\Setup\Tests\Feature\Services;

use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use Modules\Department\Contracts\Services\DepartmentService;
use Modules\Internship\Contracts\Services\InternshipService;
use Modules\School\Contracts\Services\SchoolService;
use Modules\Setting\Contracts\Services\SettingService;
use Modules\Setup\Services\SetupService;
use Modules\Shared\Exceptions\AppException;
use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\User\Contracts\Services\OwnerService;
use Tests\TestCase;

class SetupServiceTest extends TestCase
{
    private MockInterface|SettingService $settingServiceMock;

    private MockInterface|OwnerService $ownerServiceMock;

    private MockInterface|SchoolService $schoolServiceMock;

    private MockInterface|DepartmentService $departmentServiceMock;

    private MockInterface|InternshipService $internshipServiceMock;

    private SetupService $setupService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingServiceMock = Mockery::mock(SettingService::class);
        $this->ownerServiceMock = Mockery::mock(OwnerService::class);
        $this->schoolServiceMock = Mockery::mock(SchoolService::class);
        $this->departmentServiceMock = Mockery::mock(DepartmentService::class);
        $this->internshipServiceMock = Mockery::mock(InternshipService::class);

        $this->setupService = new SetupService(
            $this->settingServiceMock,
            $this->ownerServiceMock,
            $this->schoolServiceMock,
            $this->departmentServiceMock,
            $this->internshipServiceMock
        );
    }

    /** @test */
    public function test_require_setup_access_does_nothing_if_app_is_not_installed()
    {
        $this->settingServiceMock->shouldReceive('get')->with('app_installed', false)->andReturn(false);

        $this->setupService->requireSetupAccess();

        $this->assertTrue(true); // No exception was thrown
    }

    /** @test */
    public function test_require_setup_access_throws_exception_if_app_is_installed()
    {
        $this->expectException(AppException::class);

        $this->settingServiceMock->shouldReceive('get')->with('app_installed', false)->andReturn(true);

        $this->setupService->requireSetupAccess();
    }

    /** @test */
    public function test_is_step_completed_returns_true_for_completed_step()
    {
        Session::shouldReceive('get')->with('setup:welcome', false)->andReturn(true);
        $this->assertTrue($this->setupService->isStepCompleted('welcome'));
    }

    /** @test */
    public function test_is_step_completed_returns_false_for_incomplete_step()
    {
        Session::shouldReceive('get')->with('setup:welcome', false)->andReturn(false);
        $this->assertFalse($this->setupService->isStepCompleted('welcome'));
    }

    /** @test */
    public function test_is_step_completed_returns_true_for_empty_step()
    {
        $this->assertTrue($this->setupService->isStepCompleted(''));
    }

    /** @test */
    public function test_proceed_setup_step_stores_step_and_checks_record()
    {
        $this->ownerServiceMock->shouldReceive('exists')->andReturn(true);
        Session::shouldReceive('put')->with('setup:account', true)->once();

        $result = $this->setupService->proceedSetupStep('account', 'owner');

        $this->assertTrue($result);
    }

    /** @test */
    public function test_proceed_setup_step_throws_exception_if_record_not_found()
    {
        $this->expectException(RecordNotFoundException::class);

        $this->ownerServiceMock->shouldReceive('exists')->andReturn(false);
        Session::shouldReceive('put')->never();

        $this->setupService->proceedSetupStep('account', 'owner');
    }

    /** @test */
    public function test_proceed_setup_step_throws_exception_for_unknown_record_type()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->setupService->proceedSetupStep('account', 'unknown_record');
    }

    /** @test */
    public function test_finalize_app_setup_sets_settings_and_marks_app_as_installed()
    {
        $schoolMock = Mockery::mock(\Modules\School\Models\School::class)->makePartial();
        $schoolMock->name = 'Test School';
        $schoolMock->logo = 'logo.png';
        $schoolMock->logo_dark = 'logo_dark.png';

        $this->schoolServiceMock->shouldReceive('get')->andReturn($schoolMock);

        $expectedSettings = [
            'brand_name' => 'Test School',
            'brand_logo' => 'logo.png',
            'brand_logo_dark' => 'logo_dark.png',
            'site_title' => 'Test School',
            'app_installed' => true,
        ];
        $this->settingServiceMock->shouldReceive('set')->with($expectedSettings)->once();
        $this->settingServiceMock->shouldReceive('get')->with('app_installed', false)->andReturn(true);

        $result = $this->setupService->finalizeAppSetup();

        $this->assertTrue($result);
    }

    public function test_is_app_installed_returns_status_from_setting_service()
    {
        $this->settingServiceMock->shouldReceive('get')
            ->with('app_installed', false)
            ->andReturn(true, false);

        $this->assertTrue($this->setupService->isAppInstalled());
        $this->assertFalse($this->setupService->isAppInstalled());
    }
}

<?php

namespace Modules\Setup\Tests\Feature\Livewire;

use Livewire\Livewire;
use Mockery;
use Modules\Setup\Contracts\Services\SetupService;
use Modules\Setup\Livewire\SetupAccount;
use Modules\Setup\Livewire\SetupComplete;
use Modules\Setup\Livewire\SetupDepartment;
use Modules\Setup\Livewire\SetupInternship;
use Modules\Setup\Livewire\SetupSchool;
use Modules\Setup\Livewire\SetupWelcome;
use Tests\TestCase;

class SetupFlowTest extends TestCase
{
    protected $setupServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock SetupService
        $this->setupServiceMock = Mockery::mock(SetupService::class);
        $this->app->instance(SetupService::class, $this->setupServiceMock);

        // Mock routes required for redirection
        $this->app['router']->get('/setup/welcome', SetupWelcome::class)->name('setup.welcome');
        $this->app['router']->get('/setup/account', SetupAccount::class)->name('setup.account');
        $this->app['router']->get('/setup/school', SetupSchool::class)->name('setup.school');
        $this->app['router']->get('/setup/department', SetupDepartment::class)->name('setup.department');
        $this->app['router']->get('/setup/internship', SetupInternship::class)->name('setup.internship');
        $this->app['router']->get('/setup/complete', SetupComplete::class)->name('setup.complete');
        $this->app['router']->get('/login', fn () => 'Login Page')->name('login');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function welcome_step_proceeds_to_account_step()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('')->andReturn(true); // Should not be called for welcome
        $this->setupServiceMock->shouldReceive('proceedSetupStep')->with('welcome', null)->once();

        Livewire::test(SetupWelcome::class)
            ->call('nextStep')
            ->assertRedirect(route('setup.account'));
    }

    /** @test */
    public function account_step_redirects_if_previous_step_is_incomplete()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('welcome')->andReturn(false);

        Livewire::test(SetupAccount::class)
            ->assertRedirect(route('setup.welcome'));
    }

    /** @test */
    public function account_step_proceeds_to_school_step()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('welcome')->andReturn(true);
        $this->setupServiceMock->shouldReceive('proceedSetupStep')->with('account', 'owner')->once();

        Livewire::test(SetupAccount::class)
            ->call('nextStep')
            ->assertRedirect(route('setup.school'));
    }

    /** @test */
    public function school_step_proceeds_to_department_step()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('account')->andReturn(true);
        $this->setupServiceMock->shouldReceive('proceedSetupStep')->with('school', 'school')->once();

        Livewire::test(SetupSchool::class)
            ->call('nextStep')
            ->assertRedirect(route('setup.department'));
    }

    /** @test */
    public function school_step_proceeds_on_event()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('account')->andReturn(true);
        $this->setupServiceMock->shouldReceive('proceedSetupStep')->with('school', 'school')->once();

        Livewire::test(SetupSchool::class)
            ->dispatch('school-updated')
            ->assertRedirect(route('setup.department'));
    }

    /** @test */
    public function department_step_proceeds_to_internship_step()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('school')->andReturn(true);
        $this->setupServiceMock->shouldReceive('proceedSetupStep')->with('department', 'department')->once();

        Livewire::test(SetupDepartment::class)
            ->call('nextStep')
            ->assertRedirect(route('setup.internship'));
    }

    /** @test */
    public function internship_step_proceeds_to_complete_step()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('department')->andReturn(true);
        $this->setupServiceMock->shouldReceive('proceedSetupStep')->with('internship', 'internship')->once();

        Livewire::test(SetupInternship::class)
            ->call('nextStep')
            ->assertRedirect(route('setup.complete'));
    }

    /** @test */
    public function complete_step_finalizes_setup_and_redirects_to_login()
    {
        $this->setupServiceMock->shouldReceive('isStepCompleted')->with('internship')->andReturn(true);
        $this->setupServiceMock->shouldReceive('proceedSetupStep')->with('complete', null)->once();
        $this->setupServiceMock->shouldReceive('finalizeAppSetup')->once();

        Livewire::test(SetupComplete::class)
            ->call('nextStep')
            ->assertRedirect(route('login'));
    }
}

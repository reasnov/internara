<?php

use App\Providers\BindServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;

// Setup a mock filesystem for the test
beforeEach(function () {
    $this->mockFilesystem = new Filesystem;
    $this->modulesPath = base_path('tests/tmp/modules');
    $this->mockFilesystem->ensureDirectoryExists($this->modulesPath);

    // Mock the config to point to our test modules directory
    config(['modules.paths.modules' => $this->modulesPath]);
    config(['modules.namespace' => 'Tests\Tmp\Modules']);

    // Create Mock Module Structure
    $this->mockFilesystem->ensureDirectoryExists($this->modulesPath.'/TestModule/src/Contracts');
    $this->mockFilesystem->ensureDirectoryExists($this->modulesPath.'/TestModule/src/Services');

    // Create Mock Contract
    $contractContent = "<?php\n\nnamespace Tests\Tmp\Modules\TestModule\Contracts;\n\ninterface MyTestContract {}";
    $this->mockFilesystem->put($this->modulesPath.'/TestModule/src/Contracts/MyTestContract.php', $contractContent);

    // Create Mock Service
    $serviceContent = "<?php\n\nnamespace Tests\Tmp\Modules\TestModule\Services;\n\nuse Tests\Tmp\Modules\TestModule\Contracts\MyTestContract;\n\nclass MyTestService implements MyTestContract {}";
    $this->mockFilesystem->put($this->modulesPath.'/TestModule/src/Services/MyTestService.php', $serviceContent);

    // Manually include the files for the test run
    require_once $this->modulesPath.'/TestModule/src/Contracts/MyTestContract.php';
    require_once $this->modulesPath.'/TestModule/src/Services/MyTestService.php';
});

// Clean up the mock filesystem
afterEach(function () {
    $this->mockFilesystem->deleteDirectory($this->modulesPath);
});

test('BindServiceProvider automatically binds contracts to services', function () {
    /** @var Application $app */
    $app = $this->app;

    // Manually register the provider to test its logic
    (new BindServiceProvider($app))->register();

    // Assert that the container has the binding
    $this->assertTrue($app->bound("Tests\Tmp\Modules\TestModule\Contracts\MyTestContract::class"));

    // Resolve the contract from the container
    $resolvedInstance = $app->make("Tests\Tmp\Modules\TestModule\Contracts\MyTestContract::class");

    // Assert that the correct implementation was resolved
    $this->assertInstanceOf("Tests\Tmp\Modules\TestModule\Services\MyTestService::class", $resolvedInstance);
});

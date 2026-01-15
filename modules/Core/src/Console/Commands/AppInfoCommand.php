<?php

declare(strict_types=1);

namespace Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

/**
 * Class AppInfoCommand
 *
 * Displays technical identity and environment information about the Internara application.
 */
class AppInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display application identity, version, and author information';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->newLine();
        $this->components->info('Internara Application Information');

        $this->components->twoColumnDetail(
            'Application Name',
            (string) config('app.name', 'Unknown'),
        );
        $this->components->twoColumnDetail(
            'Version',
            (string) config('core.info.version', 'Unknown'),
        );
        $this->components->twoColumnDetail(
            'Series Code',
            (string) config('core.info.series_code', 'Unknown'),
        );
        $this->components->twoColumnDetail(
            'Status',
            (string) config('core.info.status', 'Unknown'),
        );

        $this->newLine();
        $this->components->info('Author Information');
        $this->components->twoColumnDetail(
            'Author',
            (string) config('core.author.name', 'Unknown'),
        );
        $this->components->twoColumnDetail(
            'GitHub',
            (string) config('core.author.github', 'Unknown'),
        );
        $this->components->twoColumnDetail(
            'Email',
            (string) config('core.author.email', 'Unknown'),
        );

        $this->newLine();
        $this->components->info('Environment Details');
        $this->components->twoColumnDetail('Laravel Version', App::version());
        $this->components->twoColumnDetail('PHP Version', PHP_VERSION);
        $this->components->twoColumnDetail('Environment', (string) App::environment());
        $this->components->twoColumnDetail(
            'Debug Mode',
            config('app.debug') ? '<fg=yellow>Enabled</>' : '<fg=green>Disabled</>',
        );
        $this->components->twoColumnDetail(
            'Database Driver',
            (string) config('database.default', 'Unknown'),
        );

        $this->newLine();

        return self::SUCCESS;
    }
}

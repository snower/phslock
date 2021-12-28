# phslock

[![Software license][ico-license]](LICENSE)
[![Latest stable][ico-version-stable]][link-packagist]
[![Latest development][ico-version-dev]][link-packagist]
[![Monthly installs][ico-downloads-monthly]][link-downloads]

High-performance distributed sync service and atomic DB. Provides good multi-core support through lock queues, high-performance asynchronous binary network protocols. Can be used for spikes, synchronization, event notification, concurrency control. https://github.com/snower/slock

# Install

```bash
composer require snower/phslock
```

# Event

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Snower\Phslock\Laravel\Facades\Phslock;

class TestSlockEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:slock-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $event = Phslock::Event("test", 5, 120, false);
        $event->set();
        return 0;
    }
}

```

# License

slock uses the MIT license, see LICENSE file for the details.
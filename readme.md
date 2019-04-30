# Playstation API

As Sony doesn't provide a public API to retrieve Playstation trophies, we have
to bypass the system to fetch them. This repository is inspired from
[tustin/psn-php](https://github.com/Tustin/psn-php) code.

## Code example

```php
use Playstation\Client;

$client = new Client();
$client->login('%%TICKET_UUID%%', '%%CODE%%'); // or %%REFRESH_TOKEN%% only

foreach ($client->user('%%USERNAME%%')->games() as $game) {
    echo "## {$game->name()}";

    if ($game->hasTrophies()) {
        foreach ($game->trophyGroups() as $group) {
            echo "# {$group->name()}";

            foreach ($group->trophies() as $trophy) {
                if ($trophy->earned()) {
                    echo "{$trophy->name()} earned";
                    continue;
                }

                echo "{$trophy->name()} not earned";
            }
        }
    }
}
```

### Laravel Integration

```php
<?php
declare(strict_types=1);
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Playstation\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // performs automatic token refresh on each call
        $this->app->singleton(Client::class, function ($app): Client {
            $client = new Client();
            $client->login($app['cache']->get('playstation:refresh_token'));

            if (
                $client->getRefreshToken() !==
                $app['cache']->get('playstation:refresh_token')
            ) {
                $app['cache']->forever(
                    'playstation:refresh_token',
                    $client->getRefreshToken()
                );
            }

            return $client;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ...
    }
}
```

## Constraints

-   Sony uses rate limit, so don't make too many requests on a single endpoint (with same query parameters);

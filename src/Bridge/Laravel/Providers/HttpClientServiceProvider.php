<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface;
use EoneoPay\Externals\HttpClient\LoggingClient;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        // Define a Guzzle binding so Client can be created
        $this->app->singleton(GuzzleClientInterface::class, GuzzleClient::class);

        $this->app->singleton(ExceptionHandlerInterface::class, ExceptionHandler::class);

        // Concrete implementations
        $this->app->singleton(ClientInterface::class, Client::class);
        $this->app->singleton(LoggingClient::class, static function (Container $app): LoggingClient {
            return new LoggingClient(
                $app->get(ClientInterface::class),
                $app->get(LoggerInterface::class)
            );
        });
    }
}

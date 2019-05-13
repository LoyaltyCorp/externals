<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface;
use EoneoPay\Externals\HttpClient\Interfaces\StreamParserInterface;
use EoneoPay\Externals\HttpClient\LoggingClient;
use EoneoPay\Externals\HttpClient\StreamParser;
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
        $this->app->bind(GuzzleClientInterface::class, GuzzleClient::class);

        $this->app->bind(ExceptionHandlerInterface::class, ExceptionHandler::class);
        $this->app->bind(StreamParserInterface::class, StreamParser::class);

        // Concrete implementations
        $this->app->bind(Client::class);
        $this->app->bind(LoggingClient::class, static function (Container $app) {
            return new LoggingClient(
                $app->get(Client::class),
                $app->get(LoggerInterface::class)
            );
        });
    }
}

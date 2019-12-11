<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Request;
use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\Request\Interfaces\RequestInterface;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\ServiceProvider;

final class RequestServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Trusted proxies must be set statically
     */
    public function register(): void
    {
        // Interface for incoming http requests
        $this->app->bind(RequestInterface::class, function (): Request {
            // Create env instance
            $env = $this->app->make(Env::class);

            // Set proxy list
            HttpRequest::setTrustedProxies(
                \explode(',', $env->get('TRUSTED_PROXIES') ?? ''),
                $this->mapTrustedHeader($env->get('TRUSTED_PROXIES_HEADER') ?? '')
            );

            return $this->app->make(Request::class);
        });
    }

    /**
     * Maps the trusted header string value to integer.
     *
     * @param string $value
     *
     * @return int
     */
    private function mapTrustedHeader(string $value): int
    {
        $name = \sprintf('%s::%s', HttpRequest::class, $value);

        // @codeCoverageIgnoreStart
        // Safety fallback, unable to alter environment values in externals to test.
        if (\defined($name) === false) {
            return HttpRequest::HEADER_X_FORWARDED_ALL;
        }
        // @codeCoverageIgnoreEnd

        $constant = \constant($name);
        if (\is_int($constant)) {
            return $constant;
        }

        // @codeCoverageIgnoreStart
        // Safety fallback, unable to alter environment values in externals to test.
        return HttpRequest::HEADER_X_FORWARDED_ALL;
        // @codeCoverageIgnoreEnd
    }
}

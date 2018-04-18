<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\ValidationServiceProvider;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\ValidationServiceProvider
 */
class ValidatorServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider bind translator and validator into container.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegister(): void
    {
        $this->getApplication()->singleton('translator', function () {
            return new Translator(new ArrayLoader(), 'en');
        });

        (new ValidationServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(Translator::class, $this->getApplication()->get(TranslatorContract::class));
        self::assertInstanceOf(ValidatorInterface::class, $this->getApplication()->get(ValidatorInterface::class));
    }
}

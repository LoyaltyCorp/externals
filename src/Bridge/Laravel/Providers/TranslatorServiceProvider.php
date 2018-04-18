<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Translator;
use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;
use Illuminate\Contracts\Translation\Translator as ContractedTranslator;
use Illuminate\Support\ServiceProvider;

class TranslatorServiceProvider extends ServiceProvider
{
    /**
     * Register translator
     *
     * @return void
     */
    public function register(): void
    {
        // Translator is required for error messages
        $this->app->bind(ContractedTranslator::class, function () {
            return $this->app->make('translator');
        });

        // Interface for translation
        $this->app->bind(TranslatorInterface::class, Translator::class);
    }
}

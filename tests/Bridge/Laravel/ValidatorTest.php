<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validator
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validation\EmptyWithRule
 */
class ValidatorTest extends TestCase
{
    /**
     * Test custom rule to empty with
     *
     * @return void
     */
    public function testValidatorCustomRuleEmptyWith(): void
    {
        $validator = $this->createValidator(
            ['empty_with' => ':attribute must be empty when :values is present']
        );

        // If key1 has a value without key2 the rule should pass
        self::assertTrue($validator->validate(
            ['key1' => 'value1'],
            ['key2' => 'empty_with:key1|string']
        ));

        // If neither key has a value the rule should pass
        self::assertTrue($validator->validate(
            [],
            ['key2' => 'empty_with:key1|string']
        ));

        // If both keys are specified but only one has a value the rule should pass
        self::assertTrue($validator->validate(
            ['key1' => 'value1', 'key2' => ''],
            ['key2' => 'empty_with:key1|string']
        ));

        // If just the tested key has a value the rule should pass
        self::assertTrue($validator->validate(
            ['key1' => '', 'key2' => 'value2'],
            ['key2' => 'empty_with:key1|string']
        ));

        // If both keys and value have values the rule should fail
        self::assertFalse($validator->validate(
            ['key1' => 'value1', 'key2' => 'value2'],
            ['key2' => 'empty_with:key1,key3|string']
        ));

        // Test failure message
        self::assertSame($validator->getFailures(), ['key2' => ['key2 must be empty when key1 / key3 is present']]);
    }

    /**
     * Test error messages work as expected
     *
     * @return void
     */
    public function testValidatorWithFailedValidation(): void
    {
        $validator = $this->createValidator();

        self::assertFalse($validator->validate(['key' => 'value'], ['missing' => 'required']));
        self::assertSame($validator->getFailures(), ['missing' => ['missing is required']]);
    }

    /**
     * Test validator can validate data
     *
     * @return void
     */
    public function testValidatorWithSuccessfulValidation(): void
    {
        self::assertTrue($this->createValidator()->validate(['key' => 'value'], ['key' => 'required|string']));
    }

    /**
     * Create validation instance
     *
     * @param array|null $messages Validation messages
     *
     * @return \EoneoPay\Externals\Bridge\Laravel\Validator
     */
    private function createValidator(?array $messages = null): Validator
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', $messages ?? ['required' => ':attribute is required']);

        return new Validator(new Factory(new Translator($loader, 'en')));
    }
}

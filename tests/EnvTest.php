<?php

use Krzysztofzylka\Env\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    /**
     * The Env instance.
     *
     * @var Env
     */
    protected Env $env;

    /**
     * This method is called before each test.
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->env = new Env(__DIR__ . '/.env.example');
    }

    /**
     * Test 'parseValue' method.
     *
     * @throws ReflectionException
     */
    public function testParseValue(): void
    {
        $method = (new ReflectionClass($this->env))->getMethod('parseValue');
        $method->setAccessible(true);

        // test integer value
        $this->assertSame(123, $method->invoke($this->env, '123'));
        // test float value
        $this->assertSame(123.456, $method->invoke($this->env, '123.456'));
        // test string value
        $this->assertSame('string', $method->invoke($this->env, "'string'"));
        $this->assertSame('string', $method->invoke($this->env, '"string"'));
        // test boolean values
        $this->assertSame(false, $method->invoke($this->env, 'false'));
        $this->assertSame(true, $method->invoke($this->env, 'true'));
        // test null value
        $this->assertSame(null, $method->invoke($this->env, 'null'));
    }

}
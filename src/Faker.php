<?php

/*
 * This file is part of Factory Muffin Faker.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FactoryMuffin\Faker;

use Closure;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Base;

/**
 * This is the faker class.
 *
 * This class is not intended to be used directly, but should be used through
 * the provided facade. The only time where you should be directly calling
 * methods here should be when you're using method chaining after initially
 * using the facade.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Faker
{
    /**
     * The generator instance.
     *
     * @var Generator|null
     */
    private ?Generator $generator;

    /**
     * The faker localization.
     *
     * @var string
     */
    private string $locale = 'en_EN';

    /**
     * Create a new faker instance.
     *
     * @param Generator|null $generator The generator instance.
     *
     * @return void
     */
    public function __construct(?Generator  $generator = null)
    {
        $this->generator = $generator;
    }

    /**
     * Set the locale.
     *
     * @param string $local The locale.
     *
     * @return Faker
     */
    public function setLocale(string $local): self
    {
        $this->locale = $local;

        $this->generator = null;

        return $this;
    }

    /**
     * Get the generator instance.
     *
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        if (null === $this->generator) {
            $this->generator = Factory::create($this->locale);
        }

        return $this->generator;
    }

    /**
     * Add a provider.
     *
     * @param Base $provider The provider instance.
     *
     * @return Faker
     */
    public function addProvider(Base $provider): self
    {
        $this->getGenerator()->addProvider($provider);

        return $this;
    }

    /**
     * Get the providers.
     *
     * @return Base[]
     */
    public function getProviders(): array
    {
        return $this->getGenerator()->getProviders();
    }

    /**
     * Wrap a faker format in a closure.
     *
     * @param string $formatter The formatter.
     * @param array  $arguments The arguments.
     *
     * @return Closure
     */
    public function format(string $formatter, array $arguments = []): Closure
    {
        $generator = $this->getGenerator();

        return function () use ($generator, $formatter, $arguments) {
            return $generator->format($formatter, $arguments);
        };
    }

    /**
     * Get a formatter.
     *
     * @param string $formatter The formatter.
     *
     * @return Closure
     */
    public function getFormatter(string $formatter): Closure
    {
        return $this->getGenerator()->getFormatter($formatter);
    }

    /**
     * Make the generated item unique.
     *
     * @param bool $reset      Should we reset the unique tracker?
     * @param int $maxRetries How many times should we retry?
     *
     * @return Faker
     */
    public function unique(bool $reset = false, int $maxRetries = 10000): self
    {
        return new static($this->getGenerator()->unique($reset, $maxRetries));
    }

    /**
     * Make the generated item optional.
     *
     * @param float $weight  The probability of not receiving the default value.
     * @param mixed $default The default item.
     *
     * @return Faker
     */
    public function optional(float $weight = 0.5, mixed $default = null): self
    {
        return new static($this->getGenerator()->optional($weight, $default));
    }

    /**
     * Dynamically wrap faker method calls in closures.
     *
     * @param string $method    The method name.
     * @param array  $arguments The arguments.
     *
     * @return Closure
     */
    public function __call(string $method, array $arguments)
    {
        return $this->format($method, $arguments);
    }
}

<?php

/*
 * This file is part of the "andrey-helldar/laravel-lang-publisher" project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Andrey Helldar <helldar@ai-rus.com>
 *
 * @copyright 2021 Andrey Helldar
 *
 * @license MIT
 *
 * @see https://github.com/andrey-helldar/laravel-lang-publisher
 */

declare(strict_types=1);

namespace Helldar\LaravelLangPublisher\Concerns;

use Helldar\LaravelLangPublisher\Facades\Helpers\Locales;
use Helldar\Support\Facades\Helpers\Arr;
use Helldar\Support\Facades\Helpers\Str;

trait Ask
{
    protected function getLocales(): array
    {
        if ($locales = $this->argument('locales')) {
            return $this->resolveSelectedLocales($locales);
        }

        return $this->askLocales($this->getMethod());
    }

    protected function askLocales(string $method): array
    {
        $locales = $this->confirm("Do you want to $method all localizations?") ? $this->getAllLocales() : $this->selectLocales($method);

        return $this->resolveSelectedLocales($locales);
    }

    protected function getAllLocales(): array
    {
        return Locales::available();
    }

    protected function selectLocales(string $method)
    {
        return $this->choice("Select localizations to $method (specify the necessary localizations separated by commas):", $this->getAllLocales(), null, null, true);
    }

    protected function resolveSelectedLocales($locales): array
    {
        $locales = Arr::wrap($locales);

        return $this->validatedLocales($locales);
    }

    protected function getMethod(): string
    {
        $name = class_basename(static::class);

        return Str::lower($name);
    }

    protected function validatedLocales(array $locales): array
    {
        foreach ($locales as $locale) {
            Locales::validate($locale);
        }

        return $locales;
    }
}

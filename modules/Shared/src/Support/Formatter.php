<?php

declare(strict_types=1);

namespace Modules\Shared\Support;

use Illuminate\Support\Str;

final class Formatter
{
    public static function path(?string ...$paths): string
    {
        $path = implode('/', array_filter($paths, fn ($path) => ! empty($path)));

        return self::normalizePath($path);
    }

    protected static function normalizePath(string $path = ''): string
    {
        $path = Str::startsWith($path, '/') ? Str::replaceFirst('/', '', $path) : $path;
        $path = Str::endsWith($path, '/') ? Str::replaceLast('/', '', $path) : $path;
        $path = Str::replace('//', '/', $path);

        return $path;
    }

    public static function namespace(?string ...$parts): string
    {
        $namespace = implode('\\', array_filter($parts, fn ($namespace) => ! empty($namespace)));

        return self::normalizeNamespace($namespace);
    }

    protected static function normalizeNamespace(string $namespace): string
    {
        $namespace = Str::startsWith($namespace, '\\')
            ? Str::replaceFirst('\\', '', $namespace)
            : $namespace;
        $namespace = Str::endsWith($namespace, '\\')
            ? Str::replaceLast('\\', '', $namespace)
            : $namespace;
        $namespace = Str::replace(['/', '//', '\\\\'], '\\', $namespace);

        return $namespace;
    }
}

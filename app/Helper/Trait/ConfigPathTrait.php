<?php

namespace App\Helper\Trait;

use Illuminate\Support\Str;
use SplFileInfo;
use ReflectionClass;

/**
 * Summary of ConfigPathTrait
 * @author PutrimakIslan
 * @copyright (c) 2023
 */
trait ConfigPathTrait
{
    protected function getCurrentPath(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return dirname($reflection->getFileName());
    }

    protected function getNamespace(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return $reflection->getNamespaceName();
    }

    protected function getClassFromFile(SplFileInfo $file): string
    {
        $className = $this->getNamespace() . '\\' . str_replace(
            ['/', '.php'],
            ['\\', ''],
            Str::after($file->getPathname(), $this->getCurrentPath() . '/')
        );

        return preg_replace('/\\+/', '\\', $className);
    }

    protected function getModuleName(): string
    {
        $reflection = new ReflectionClass(get_class($this));

        return Str::kebab(preg_replace('/Module$/', '', $reflection->getShortName()));
    }

    protected function getModelPath(): string
    {
        $reflection = new ReflectionClass(get_class($this));
        $model = str_replace('Repository', '', $reflection->getShortName());
        $namespace = str_replace('\\Repository', '\\Model\\', $this->getNamespace());

        return $namespace . $model;
    }
}

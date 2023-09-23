<?php

namespace App\Helper\Ddd;

/**
 * Summary of ModelInterface
 * @author PutrimakIslan
 * @copyright (c) 2023
 */
interface ModelInterface
{
    public static function getResourceName(): string;

    public static function getModelClass(): ?string;

    public static function getRepositoryClass(): ?string;

    public function getMappedModel();
}

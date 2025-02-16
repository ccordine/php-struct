<?php

namespace Gryph\PHPStruct;

use stdClass;
use Exception;
use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;

class Nullable {}

class Struct
{
    public function __construct(array $arr = [])
    {
        foreach ($this->keys()['all'] as $key) {
            $reflectionProperty = new ReflectionProperty($this, $key);
            $value = data_get($arr, $key);
            $isNullable = $this->isNullableProperty($key);
            $expectedType = $this->getPropertyType($key);

            if (array_key_exists($key, $arr)) {
                $value = $arr[$key];
            } else {
                if ($reflectionProperty->isDefault()) {
                    $value = $reflectionProperty->getDefaultValue();
                } else {
                    $value = null;
                }
            }

            $isValid = $this->isValidType($value, $expectedType);

            if ($value === null && ! $isNullable) {
                throw new Exception("Property '{$key}' must not be null.");
            }

            if ($value !== null && ! $isValid) {
                throw new Exception("Property '{$key}' must be of type '{$expectedType}', " . gettype($value) . ' given.');
            }

            $this->$key = $value;
        }
    }

    private function isNullableProperty(string $property): bool
    {
        $reflectionProperty = new ReflectionProperty($this, $property);

        // Check if the #[Nullable] attribute is present
        if (count($reflectionProperty->getAttributes(Nullable::class)) > 0) {
            return true;
        }

        // Check if the type hint allows null (e.g., ?string)
        $type = $reflectionProperty->getType();
        if ($type !== null) {
            return $type->allowsNull();
        }

        return false;
    }

    private function getPropertyType(string $property): ?string
    {
        $reflectionProperty = new ReflectionProperty($this, $property);
        $type = $reflectionProperty->getType();

        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        return null;
    }

    private function isValidType($value, ?string $expectedType): bool
    {

        if (! $expectedType) {
            return true; // No type hint = assume it's valid
        }

        $actualType = gettype($value);

        // Normalize type mapping for compatibility
        $typeMap = [
            'integer' => 'int',
            'double' => 'float',
            'boolean' => 'bool',
            'string' => 'string',
            'array' => 'array',
            'object' => 'object',
        ];

        return isset($typeMap[$actualType]) && $typeMap[$actualType] === $expectedType;
    }

    final protected function keys(): array
    {
        // Create a ReflectionClass instance
        $reflectionClass = new ReflectionClass(get_class($this));

        // Get all properties
        $allProperties = $reflectionClass->getProperties();

        // Separate public and private properties
        $publicProperties = [];
        $privateProperties = [];
        $protectedProperties = [];

        foreach ($allProperties as $property) {
            if ($property->isPublic()) {
                $publicProperties[] = $property->getName();
            } elseif ($property->isPrivate()) {
                $privateProperties[] = $property->getName();
            } elseif ($property->isProtected()) {
                $protectedProperties[] = $property->getName();
            }
        }

        return [
            'public' => $publicProperties,
            'private' => $privateProperties,
            'protected' => $protectedProperties,
            'all' => array_merge($publicProperties, $privateProperties, $protectedProperties),
        ];
    }

    public function json(): string
    {
        return json_encode($this);
    }

    public function data(): stdClass
    {
        return json_decode($this->json());
    }

    public function array(): array
    {
        return json_decode($this->json(), true);
    }

    public static function generate(array $attributes = []): static
    {
        return new static($attributes);
    }
}

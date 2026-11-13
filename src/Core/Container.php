<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

class Container
{
    private static ?self $instance = null;
    private array $bindings = [];
    private array $instances = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function bind(string $abstract, mixed $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null; // Mark as singleton to be resolved
    }

    public function get(string $abstract): mixed
    {
        // Return existing singleton instance if already resolved
        if (array_key_exists($abstract, $this->instances) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        // If it's a Closure / callable
        if ($concrete instanceof \Closure) {
            $resolved = $concrete($this);
        } else {
            $resolved = $this->resolve($concrete);
        }

        // Cache if registered as singleton
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $resolved;
        }

        return $resolved;
    }

    public function has(string $abstract): bool
    {
        return array_key_exists($abstract, $this->bindings) || array_key_exists($abstract, $this->instances);
    }

    public function resolve(string $className): mixed
    {
        if (!class_exists($className)) {
            throw new RuntimeException("Clase '{$className}' no encontrada para inyección de dependencias.");
        }

        $reflector = new ReflectionClass($className);
        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("La clase '{$className}' no es instanciable (interfaz o clase abstracta sin binding).");
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            return new $className();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $dependencyName = $type->getName();
                if ($this->has($dependencyName)) {
                    $dependencies[] = $this->get($dependencyName);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    $dependencies[] = $this->get($dependencyName);
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new RuntimeException("No se pudo resolver el parámetro '{$parameter->getName()}' de '{$className}'.");
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}


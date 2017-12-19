<?php

namespace Dgame\Hydrator;

use Dgame\Object\ObjectFacade;

/**
 * Class Hydrator
 * @package Dgame\Hydrator
 */
final class Hydrator
{
    /**
     * @var callable[]
     */
    private $callbacks = [];
    /**
     * @var array
     */
    private $aliase = [];
    /**
     * @var array
     */
    private $ignore = [];

    /**
     * @param string   $name
     * @param callable $callback
     */
    public function setCallback(string $name, callable $callback): void
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * @param callable[] $callbacks
     */
    public function setCallbacks(array $callbacks): void
    {
        foreach ($callbacks as $name => $callback) {
            $this->setCallback($name, $callback);
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasCallback(string $name): bool
    {
        return array_key_exists($name, $this->callbacks);
    }

    /**
     * @param string $name
     *
     * @return callable
     */
    public function getCallback(string $name): callable
    {
        return $this->callbacks[$name];
    }

    /**
     * @param array $aliase
     */
    public function setAliase(array $aliase): void
    {
        $this->aliase = $aliase;
    }

    /**
     * @param string $alias
     *
     * @return bool
     */
    public function hasAlias(string $alias): bool
    {
        return array_key_exists($alias, $this->aliase);
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    public function getAlias(string $alias): string
    {
        return $this->aliase[$alias];
    }

    /**
     * @param string $name
     */
    public function ignore(string $name): void
    {
        $this->ignore[] = $name;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isIgnored(string $name): bool
    {
        return in_array($name, $this->ignore);
    }

    /**
     * @param object $object
     * @param array  $values
     *
     * @return object
     */
    public function hydrate($object, array $values)
    {
        $facade = new ObjectFacade($object);
        foreach ($values as $name => $value) {
            $name = $this->resolveAlias($name);
            if ($this->isIgnored($name)) {
                continue;
            }
            $value = $this->applyCallback($name, $value);
            $facade->setValue($name, $value);
        }

        return $facade->getObject();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function resolveAlias(string $name)
    {
        return $this->hasAlias($name) ? $this->getAlias($name) : $name;
    }

    /**
     * @param string $name
     * @param        $value
     *
     * @return mixed
     */
    private function applyCallback(string $name, $value)
    {
        if ($this->hasCallback($name)) {
            $value = $this->getCallback($name)($value, $this);
        }

        return $value;
    }
}
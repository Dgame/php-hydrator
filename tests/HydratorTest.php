<?php

namespace Dgame\Hydrator\Test;

use DateTime;
use Dgame\Hydrator\Hydrator;
use PHPUnit\Framework\TestCase;

/**
 * Class HydratorTest
 * @package Dgame\Hydrator\Test
 */
final class HydratorTest extends TestCase
{
    private function getObject()
    {
        return new class()
        {
            public  $foo;
            private $bar;
            private $lastUsage;

            public function setFoo(string $value): void
            {
                $this->foo = $value;
            }

            public function setBar(int $value): void
            {
                $this->bar = $value;
            }

            public function setLastUsage(DateTime $usage): void
            {
                $this->lastUsage = $usage;
            }

            public function getFoo(): string
            {
                return $this->foo;
            }

            public function getBar(): int
            {
                return $this->bar;
            }

            public function getLastUsage(): DateTime
            {
                return $this->lastUsage;
            }
        };
    }

    public function testExtractionOfFullAssignedObject()
    {
        $hydrator = new Hydrator();
        $hydrator->setAliase(['last-usage' => 'lastUsage']);
        $hydrator->setCallback('lastUsage', function (string $date): DateTime {
            return new DateTime($date);
        });

        $values = ['foo' => 'abc', 'bar' => 42, 'last-usage' => '12.07.2008'];
        $object = $hydrator->hydrate($this->getObject(), $values);

        $hydrator->setAliase(['lastUsage' => 'last-usage']);
        $hydrator->setCallback('last-usage', function (DateTime $date): string {
            return $date->format('d.m.Y');
        });
        $result = $hydrator->extract($object);

        $this->assertEquals($values, $result);
    }

    public function testExtractionOfNonAssignedObject()
    {
        $hydrator = new Hydrator();
        $result   = $hydrator->extract($this->getObject());
        $this->assertEmpty(array_filter($result));
    }
}
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
    public function testHydration()
    {
        $object = new class()
        {
            public  $foo;
            private $bar;
            private $lastUsage;

            public function setFoo($value): void
            {
                print 'setFoo' . PHP_EOL;
                $this->foo = $value;
            }

            public function setBar($value): void
            {
                print 'setBar' . PHP_EOL;
                $this->bar = $value;
            }

            public function setLastUsage(DateTime $usage): void
            {
                print 'setLastUsage' . PHP_EOL;
                $this->lastUsage = $usage;
            }

            /**
             * @return mixed
             */
            public function getFoo()
            {
                return $this->foo;
            }

            /**
             * @return mixed
             */
            public function getBar()
            {
                return $this->bar;
            }

            /**
             * @return DateTime
             */
            public function getLastUsage(): DateTime
            {
                return $this->lastUsage;
            }
        };

        ob_start();
        $hydrator = new Hydrator();
        $hydrator->setAliase(['last-usage' => 'lastUsage']);
        $hydrator->setCallback('lastUsage', function (string $date): DateTime {
            return new DateTime($date);
        });
        $obj     = $hydrator->hydrate($object, ['foo' => 42, 'bar' => 'hufflepuff', 'last-usage' => '12.07.2008']);
        $content = ob_get_clean();

        $this->assertSame($object, $obj);
        $this->assertEquals(42, $obj->foo);
        $this->assertEquals(42, $obj->getFoo());
        $this->assertEquals('hufflepuff', $obj->getBar());
        $this->assertEquals('12.07.2008', $obj->getLastUsage()->format('d.m.Y'));
        $this->assertEquals(['setBar', 'setLastUsage'], array_filter(explode(PHP_EOL, $content)));
    }
}
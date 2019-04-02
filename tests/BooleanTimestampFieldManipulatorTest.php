<?php

use Illuminate\Database\Capsule\Manager;

class BooleanTimestampFieldManipulatorTest extends PHPUnit_Framework_TestCase
{
    private static $user, $user2;

    public static function setupBeforeClass()
    {
        $manager = new Manager();
        $manager->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        $manager->setEventDispatcher(new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container()));

        $manager->setAsGlobal();
        $manager->bootEloquent();

        $manager->schema()->create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamp('is_active')->nullable();
            $table->timestamps();
        });

        self::$user = Tests\Entities\User::create([
            'name' => 'Syed Abidur Rahman',
            'is_active' => 1
        ]);

        self::$user2 = Tests\Entities\User::create([
            'name' => 'Md Sadiqur Rahman',
            'is_active' => 0
        ]);
    }

    /** @test */
    public function it_checks_timestamp_boolean_field_value_when_creating_a_model()
    {
        $this->assertNotNull(self::$user->is_active);
        $this->assertTrue(self::$user->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, self::$user->time_being_active);

        $this->assertNotNull(self::$user2->is_active);
        $this->assertFalse(self::$user2->is_active);
        $this->assertNull(self::$user2->time_being_active);
    }

    /** @test */
    public function it_checks_timestamp_boolean_field_value_when_updating_a_model()
    {
        self::$user->update(['is_active' => 0]);
        $this->assertNotNull(self::$user->is_active);
        $this->assertFalse(self::$user->is_active);
        $this->assertNull(self::$user->time_being_active);

        self::$user2->update(['is_active' => 1]);
        $this->assertNotNull(self::$user2->is_active);
        $this->assertTrue(self::$user2->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, self::$user2->time_being_active);
    }
}

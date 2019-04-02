<?php

use Illuminate\Database\Capsule\Manager;

class BooleanTimestampFieldManipulatorTest extends PHPUnit_Framework_TestCase
{
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
    }

    /** @test */
    public function it_checks_timestamp_boolean_field_value_when_creating_a_model()
    {
        $user = Tests\Entities\User::create([
            'name' => 'Syed Abidur Rahman',
            'is_active' => 1
        ]);

        $this->assertNotNull($user->is_active);
        $this->assertTrue($user->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->time_being_active);

        $user2 = Tests\Entities\User::create([
            'name' => 'Md Sadiqur Rahman',
            'is_active' => 0
        ]);

        $this->assertNotNull($user2->is_active);
        $this->assertFalse($user2->is_active);
        $this->assertNull($user2->time_being_active);
    }

    /** @test */
    public function it_checks_timestamp_boolean_field_value_when_updating_a_model()
    {
        $user = Tests\Entities\User::where('name', 'Syed Abidur Rahman')->first();
        if (empty($user)) {
            $user = Tests\Entities\User::create([
                'name' => 'Syed Abidur Rahman',
                'is_active' => 1
            ]);
        }

        $user->update(['is_active' => 0]);

        $this->assertNotNull($user->is_active);
        $this->assertFalse($user->is_active);
        $this->assertNull($user->time_being_active);

        $user2 = Tests\Entities\User::where('name', 'Md Sadiqur Rahman')->first();
        if (empty($user2)) {
            $user2 = Tests\Entities\User::create([
                'name' => 'Md Sadiqur Rahman',
                'is_active' => 0
            ]);
        }

        $user2->update(['is_active' => 1]);

        $this->assertNotNull($user2->is_active);
        $this->assertTrue($user2->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user2->time_being_active);
    }
}

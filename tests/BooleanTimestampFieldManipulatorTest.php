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
    }
}

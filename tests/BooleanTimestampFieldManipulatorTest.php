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

        $manager->schema()->create('notes', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->timestamp('is_unpublished')->nullable();
            $table->timestamp('is_reported')->nullable();
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

    /** @test */
    public function it_checks_timestamp_boolean_field_value_when_updating_a_fetched_model_from_database()
    {
        $user = Tests\Entities\User::where('name', 'Syed Abidur Rahman')->first();
        $user->update(['is_active' => 0]);
        $this->assertNotNull($user->is_active);
        $this->assertFalse($user->is_active);
        $this->assertNull($user->time_being_active);
        $user->update(['is_active' => 1]);

        $user2 = Tests\Entities\User::where('name', 'Md Sadiqur Rahman')->first();
        $user2->update(['is_active' => 1]);
        $this->assertNotNull($user2->is_active);
        $this->assertTrue($user2->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user2->time_being_active);
        $user2->update(['is_active' => 0]);
    }

    /** @test */
    public function it_checks_timestamp_boolean_field_value_when_updating_not_the_bool_timestamp_fields_of_a_model()
    {
        $user = Tests\Entities\User::where('name', 'Syed Abidur Rahman')->first();
        $user->update(['name' => 'Abidur Rahman']);
        $this->assertNotNull($user->is_active);
        $this->assertTrue($user->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->time_being_active);

        $user2 = Tests\Entities\User::where('name', 'Md Sadiqur Rahman')->first();
        $user2->update(['name' => 'Sadiqur Rahman']);
        $this->assertNotNull($user2->is_active);
        $this->assertFalse($user2->is_active);
        $this->assertNull($user2->time_being_active);
    }

    /** @test */
    public function it_checks_timestamp_boolean_field_value_when_a_model_has_more_than_one_bool_timestamp_field()
    {
        $note = Tests\Entities\Note::create([
            'user_id' => 1,
            'title' => 'Sample Note',
            'description' => 'This is just sample note!',
            'is_unpublished' => false,
            'is_reported' => true
        ]);

        $this->it_checks_timestamp_boolean_field_value_when_a_model_having_more_than_one_bool_timestamp_field($note);


        Tests\Entities\Note::create([
            'user_id' => 2,
            'title' => 'Sample Note 2',
            'description' => 'This is 2nd sample note!',
            'is_unpublished' => false,
            'is_reported' => true
        ]);

        $dbNote = Tests\Entities\Note::where('description', 'This is 2nd sample note!')->first();
        $this->it_checks_timestamp_boolean_field_value_when_a_model_having_more_than_one_bool_timestamp_field($dbNote);
    }

    private function it_checks_timestamp_boolean_field_value_when_a_model_having_more_than_one_bool_timestamp_field(\Tests\Entities\Note $note)
    {
        $this->assertNotNull($note->is_unpublished);
        $this->assertFalse($note->is_unpublished);
        $this->assertNull($note->time_being_unpublished);

        $this->assertNotNull($note->is_reported);
        $this->assertTrue($note->is_reported);
        $this->assertInstanceOf(\Carbon\Carbon::class, $note->time_being_reported);


        $note->update(['title' => 'Sample Note!!!']);

        $this->assertNotNull($note->is_unpublished);
        $this->assertFalse($note->is_unpublished);
        $this->assertNull($note->time_being_unpublished);

        $this->assertNotNull($note->is_reported);
        $this->assertTrue($note->is_reported);
        $this->assertInstanceOf(\Carbon\Carbon::class, $note->time_being_reported);


        $note->update(['is_unpublished' => true]);

        $this->assertNotNull($note->is_unpublished);
        $this->assertTrue($note->is_unpublished);
        $this->assertInstanceOf(\Carbon\Carbon::class, $note->time_being_unpublished);

        $this->assertNotNull($note->is_reported);
        $this->assertTrue($note->is_reported);
        $this->assertInstanceOf(\Carbon\Carbon::class, $note->time_being_reported);


        $note->update(['is_reported' => true]);

        $this->assertNotNull($note->is_unpublished);
        $this->assertTrue($note->is_unpublished);
        $this->assertInstanceOf(\Carbon\Carbon::class, $note->time_being_unpublished);

        $this->assertNotNull($note->is_reported);
        $this->assertTrue($note->is_reported);
        $this->assertInstanceOf(\Carbon\Carbon::class, $note->time_being_reported);


        $note->update(['is_reported' => false]);

        $this->assertNotNull($note->is_unpublished);
        $this->assertTrue($note->is_unpublished);
        $this->assertInstanceOf(\Carbon\Carbon::class, $note->time_being_unpublished);

        $this->assertNotNull($note->is_reported);
        $this->assertFalse($note->is_reported);
        $this->assertNull($note->time_being_reported);


        $note->update(['is_unpublished' => false]);

        $this->assertNotNull($note->is_unpublished);
        $this->assertFalse($note->is_unpublished);
        $this->assertNull($note->time_being_unpublished);

        $this->assertNotNull($note->is_reported);
        $this->assertFalse($note->is_reported);
        $this->assertNull($note->time_being_reported);
    }
}

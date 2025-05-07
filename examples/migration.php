<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The connection name for the migration.
     */
    protected $connection = 'iseries';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('MYUSERS', function (Blueprint $table)
        {
            $table->bigInteger('id')->primary();
            $table->string('name');
            $table->boolean('status')->default(true);

            $table->setSystemName('MYUSERS01F');
        });

        Schema::create('MYTABLE', function (Blueprint $table)
        {
            $table->binary('binary');
            $table->bigInteger('bigInteger')->primary();
            $table->char('char')->index();
            $table->date('date')->nullable();
            $table->dateTime('dateTime');
            $table->dateTimeTz('dateTimeTz');
            $table->decimal('decimal');
            $table->double('double');
            $table->float('float')->index();
            $table->integer('integer');
            $table->ipAddress('ipAddress')->nullable();
            $table->json('json');
            $table->jsonb('jsonb')->nullable();
            $table->longText('longText');
            $table->macAddress('macAddress')->unique();
            $table->mediumInteger('mediumInteger');
            $table->mediumText('mediumText');
            $table->morphs('morphs');
            $table->nullableUlidMorphs('nullableUlidMorphs');
            $table->nullableUuidMorphs('nullableUuidMorphs');
            $table->nullableMorphs('nullableMorphs');
            $table->rememberToken('rememberToken');
            $table->smallInteger('smallInteger');
            $table->softDeletes();
            $table->string('string')->index();
            $table->text('text');
            $table->time('time');
            $table->timestamp('timestamp')->nullable();
            $table->timestamps();
            $table->timestampTz('timestampTz');
            $table->timeTz('timeTz');
            $table->tinyInteger('tinyInteger');
            $table->tinyText('tinyText');
            $table->ulid('ulid')->unique();
            $table->ulidMorphs('ulidMorphs');
            $table->unsignedBigInteger('unsignedBigInteger');
            $table->unsignedInteger('unsignedInteger');
            $table->unsignedMediumInteger('unsignedMediumInteger');
            $table->unsignedSmallInteger('unsignedSmallInteger');
            $table->unsignedTinyInteger('unsignedTinyInteger');
            $table->uuid('uuid');
            $table->uuidMorphs('uuidMorphs');
            $table->year('year');
        
            $table->setSystemName('MYTABLE01F');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MYUSERS');
        Schema::dropIfExists('MYTABLE');
    }
};

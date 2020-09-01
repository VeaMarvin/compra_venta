<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Categoria::class, function (Faker $faker) {
    return [
        'nombre' => $faker->unique(50)->jobTitle,
        'descripcion' => $faker->text(125),
    ];
});

$factory->define(App\Articulo::class, function (Faker $faker) {
    return [
        'id_categoria' => App\Categoria::all()->random()->id,
        'codigo' => $faker->unique()->randomElement([$faker->bothify('########'),$faker->bothify('?###??###')]),
        'nombre' => $faker->unique()->jobTitle,
        'precio_venta' => $faker->randomFloat(2, 1, 100),
        'stock' => $faker->numerify('####'),
        'descripcion' => $faker->text(200),
    ];
});

$factory->define(App\Persona::class, function (Faker $faker) {
    return [
        'nombre' => $faker->unique()->randomElement([$faker->firstNameMale.' '.$faker->lastName, $faker->firstNameFemale.' '.$faker->lastName]),
        'tipo_documento' => App\Persona::TIPO_DOCUMENTO,
        'numero_documento' => $faker->randomElement([$faker->bothify('######-?'), $faker->bothify('######-#'), 'CF']),
        'direccion' => $faker->randomElement([$faker->address,'CF']),
        'telefono' => $faker->randomElement([$faker->phoneNumber,'']),
        'email' => $faker->randomElement([$faker->safeEmail,'']),
    ];
});

$factory->define(App\Proveedor::class, function (Faker $faker) {
    return [
        'id_persona' => App\Persona::all()->random()->id,
        'contacto' => $logico = $faker->randomElement([$faker->company, '']),
        'telefono_contacto' => $logico == '' ? '' : mb_strtoupper($faker->numerify('########')),
    ];
});
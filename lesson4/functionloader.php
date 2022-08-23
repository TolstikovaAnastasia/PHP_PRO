<?php

//require_once __DIR__ . "/vendor/autoload.php";

use Anastasia\Rpg\Exceptions\PetNotFoundException;
use Anastasia\Rpg\Units\{Hero, Pet};
use Anastasia\Rpg\Repositories\InMemoryPetsRepo;

spl_autoload_register('loadClassName');

function loadClassName($className)
{
    $fileName = str_replace('_', '/', $className);
    $fileName = str_replace('\\', '/', $fileName);
    $fileName = str_replace('Anastasia/Rpg/', 'src/', $fileName) . ".php";

    if (file_exists($fileName)) {
        include $fileName;
    }
}

try {
    $faker = Faker\Factory::create();

    $hero1 = new Hero(1, $faker->name(), 150);


    $repo = new InMemoryPetsRepo();

    for ($i = 0; $i < 10; $i++) {
        $pet = new Pet($i, $faker->name(), 50);
        $repo->save($pet);
    }

    echo $repo->get(15);
} catch (PetNotFoundException $exception) {
    echo $exception->getMessage();
} catch (Exception $exception) {
    print_r($exception->getTrace());
}

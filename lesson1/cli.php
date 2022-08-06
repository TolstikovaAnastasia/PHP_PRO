<?php

require_once __DIR__ . "/vendor/autoload.php";

use Anastasia\Blog\Blogs\{User, Article, Comment};

$faker = Faker\Factory::create('ru_Ru');

$string = 'Введите author, article или comment' . PHP_EOL;

if (empty($argv[1])) {
    die($string);
} else {
    $input = $argv[1];
}

switch ($input) {
    case 'author':
        $authorId = (int)$faker->randomNumber();
        $firstName = $faker->firstName();
        $lastName = $faker->lastName();

        echo new User($authorId, $firstName, $lastName);

        break;

    case 'article':
        $articleId = (int)$faker->randomNumber();
        $articleHeader = $faker->realText($faker->numberBetween(20, 60));
        $articleText = $faker->realText($faker->numberBetween(100, 200));

        echo new Article($articleId, $authorId, $articleHeader, $articleText);

        break;

    case 'comment':
        $commentId = (int)$faker->randomNumber();
        $authorId = (int)$faker->randomNumber();
        $articleId = (int)$faker->randomNumber();
        $commentText = $faker->realText($faker->numberBetween(80, 100));

        echo new Comment($commentId, $authorId, $articleId, $commentText);

        break;

    default:
        echo $string;
}

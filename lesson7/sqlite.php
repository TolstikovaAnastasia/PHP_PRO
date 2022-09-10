<?php

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$connection->exec(
    "INSERT INTO users (firstName, lastName) VALUES ('Anastasia', 'Volkova')"
);

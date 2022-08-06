<?php

namespace Anastasia\Rpg\Repositories;

use Anastasia\Rpg\Units\Pet;
use Anastasia\Rpg\Exceptions\PetNotFoundException;

class InMemoryPetsRepo
{
    private array $pets = [];

    public function save(Pet $pet): void
    {
        $this->pets[] = $pet;
    }

    public function get(int $id): Pet
    {
        foreach ($this->pets as $pet) {
            if ($pet->id() === $id) {
                return $pet;
            }
        }
        throw new PetNotFoundException('Нет такого pets', 404);
    }
}

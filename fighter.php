<?php

class Fighter
{
    public int $cardId;
    public string $name;

    public int $maxHp;
    public int $hp;

    public int $attack;

    public string $ability;

    public bool $alive = true;

    public int $shield = 0;

    public array $status = [];

    public function __construct(int $cardId, array $cards, array $battleStats)
    {
        $this->cardId = $cardId;

        $this->name = $cards[$cardId]['name'];

        $this->maxHp = $battleStats[$cardId]['health'];
        $this->hp = $battleStats[$cardId]['health'];

        $this->attack = $battleStats[$cardId]['attack'];

        $this->ability = $cards[$cardId]['ability'];
    }

    public function takeDamage(int $damage): int
    {
        if ($this->shield > 0) {

            $damage = max(0, $damage - $this->shield);

            $this->shield = 0;
        }

        $this->hp -= $damage;

        if ($this->hp <= 0) {

            $this->hp = 0;

            $this->alive = false;
        }

        return $damage;
    }

    public function heal(int $amount): void
    {
        $this->hp += $amount;

        if ($this->hp > $this->maxHp) {

            $this->hp = $this->maxHp;
        }
    }

    public function addShield(int $amount): void
    {
        $this->shield += $amount;
    }

    public function isAlive(): bool
    {
        return $this->alive;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getAttack(): int
    {
        return $this->attack;
    }

    public function getAbility(): string
    {
        return $this->ability;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

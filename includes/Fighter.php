<?php

class Fighter
{
    private int $cardId;
    private string $name;
    private string $image;
    private string $ability;

    private int $maxHp;
    private int $hp;
    private int $attack;
    private int $shield = 0;
    private bool $alive = true;
    private array $status = [];

    public function __construct(int $cardId, array $cards, array $battleStats)
    {
        if (!isset($cards[$cardId], $battleStats[$cardId])) {
            throw new InvalidArgumentException('Ungültige Karten-ID: ' . $cardId);
        }

        $this->cardId = $cardId;
        $this->name = (string)($cards[$cardId]['name'] ?? 'Unbekannte Karte');
        $this->image = (string)($cards[$cardId]['image'] ?? '');
        $this->ability = (string)($cards[$cardId]['ability'] ?? 'attack');
        $this->maxHp = max(1, (int)($battleStats[$cardId]['health'] ?? 1));
        $this->hp = $this->maxHp;
        $this->attack = max(1, (int)($battleStats[$cardId]['attack'] ?? 1));
    }

    public function takeDamage(int $damage): int
    {
        $damage = max(0, $damage);

        if ($this->shield > 0 && $damage > 0) {
            $blocked = min($this->shield, $damage);
            $damage -= $blocked;
            $this->shield -= $blocked;
        }

        $this->hp -= $damage;

        if ($this->hp <= 0) {
            $this->hp = 0;
            $this->alive = false;
        }

        return $damage;
    }

    public function heal(int $amount): int
    {
        $amount = max(0, $amount);
        $before = $this->hp;
        $this->hp = min($this->maxHp, $this->hp + $amount);

        return $this->hp - $before;
    }

    public function addShield(int $amount): int
    {
        $amount = max(0, $amount);
        $this->shield += $amount;

        return $amount;
    }

    public function getCardId(): int
    {
        return $this->cardId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getAbility(): string
    {
        return $this->ability;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getMaxHp(): int
    {
        return $this->maxHp;
    }

    public function getAttack(): int
    {
        return $this->attack;
    }

    public function getShield(): int
    {
        return $this->shield;
    }

    public function isAlive(): bool
    {
        return $this->alive;
    }

    public function toArray(): array
    {
        return [
            'card_id' => $this->cardId,
            'name' => $this->name,
            'image' => $this->image,
            'ability' => $this->ability,
            'hp' => $this->hp,
            'max_hp' => $this->maxHp,
            'attack' => $this->attack,
            'shield' => $this->shield,
            'alive' => $this->alive,
            'status' => $this->status,
        ];
    }
}

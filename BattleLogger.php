<?php

class BattleLogger
{
    private array $log = [];

    public function add(
        int $round,
        string $type,
        string $attacker,
        string $target,
        int $value,
        int $targetHp
    ): void {

        $this->log[] = [
            'round' => $round,
            'type' => $type,
            'attacker' => $attacker,
            'target' => $target,
            'value' => $value,
            'target_hp' => $targetHp,
            'time' => microtime(true)
        ];
    }

    public function addText(
        int $round,
        string $text
    ): void {

        $this->log[] = [
            'round' => $round,
            'type' => 'text',
            'text' => $text,
            'time' => microtime(true)
        ];
    }

    public function getLog(): array
    {
        return $this->log;
    }

    public function clear(): void
    {
        $this->log = [];
    }
}
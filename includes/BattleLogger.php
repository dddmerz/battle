<?php

class BattleLogger
{
    private array $log = [];

    public function event(int $turn, string $type, array $data = []): void
    {
        $this->log[] = array_merge([
            'turn' => $turn,
            'type' => $type,
        ], $data);
    }

    public function getLog(): array
    {
        return $this->log;
    }
}

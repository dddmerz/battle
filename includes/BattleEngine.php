<?php

require_once __DIR__ . '/Fighter.php';
require_once __DIR__ . '/BattleLogger.php';
require_once __DIR__ . '/BattleFunctions.php';

class BattleEngine
{
    public function run(Fighter $creator, Fighter $opponent): array
    {
        $logger = new BattleLogger();
        $turn = 1;

        while ($creator->isAlive() && $opponent->isAlive() && $turn <= 50) {
            BattleFunctions::performTurn($creator, $opponent, $logger, $turn);

            if (!$opponent->isAlive()) {
                break;
            }

            BattleFunctions::performTurn($opponent, $creator, $logger, $turn);
            $turn++;
        }

        $winner = 'draw';

        if ($creator->isAlive() && !$opponent->isAlive()) {
            $winner = 'creator';
        } elseif ($opponent->isAlive() && !$creator->isAlive()) {
            $winner = 'opponent';
        } elseif ($creator->getHp() > $opponent->getHp()) {
            $winner = 'creator';
        } elseif ($opponent->getHp() > $creator->getHp()) {
            $winner = 'opponent';
        }

        return [
            'winner' => $winner,
            'creator' => $creator->toArray(),
            'opponent' => $opponent->toArray(),
            'log' => $logger->getLog(),
        ];
    }
}

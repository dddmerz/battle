<?php

require_once __DIR__ . '/Fighter.php';
require_once __DIR__ . '/BattleLogger.php';

class BattleFunctions
{
    public static function performTurn(Fighter $a, Fighter $b, BattleLogger $logger, int $turn): void
    {
        $ability = $a->getAbility();

        if ($ability === 'shield') {
            $shield = $a->addShield(3);
            $logger->event($turn, 'shield', [
                'actor' => $a->getName(),
                'target' => $a->getName(),
                'value' => $shield,
                'target_hp' => $a->getHp(),
                'target_max_hp' => $a->getMaxHp(),
                'target_shield' => $a->getShield(),
            ]);
        }

        $value = $a->getAttack();
        $type = 'attack';

        if ($ability === 'critical' && random_int(1, 100) <= 20) {
            $value *= 2;
            $type = 'critical';
        }

        if ($ability === 'lightning') {
            $value += 2;
            $type = 'lightning';
        }

        if ($ability === 'legendary' && random_int(1, 100) <= 50) {
            $value += 8;
            $type = 'legendary';
        }

        $done = $b->takeDamage($value);

        $logger->event($turn, $type, [
            'actor' => $a->getName(),
            'target' => $b->getName(),
            'value' => $done,
            'target_hp' => $b->getHp(),
            'target_max_hp' => $b->getMaxHp(),
            'target_shield' => $b->getShield(),
        ]);

        if ($b->isAlive() && $ability === 'heal') {
            $heal = $a->heal(random_int(2, 4));
            if ($heal > 0) {
                $logger->event($turn, 'heal', [
                    'actor' => $a->getName(),
                    'target' => $a->getName(),
                    'value' => $heal,
                    'target_hp' => $a->getHp(),
                    'target_max_hp' => $a->getMaxHp(),
                    'target_shield' => $a->getShield(),
                ]);
            }
        }
    }
}

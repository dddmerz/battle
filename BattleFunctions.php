<?php

require_once __DIR__ . '/Fighter.php';

class BattleFunctions
{
    /**
     * Führt einen normalen Angriff aus.
     */
    public static function attack(Fighter $attacker, Fighter $defender): int
    {
        return $defender->takeDamage($attacker->getAttack());
    }

    /**
     * Kritischer Treffer
     * Standard: 20% Chance auf doppelten Schaden
     */
    public static function criticalAttack(Fighter $attacker, Fighter $defender): int
    {
        $damage = $attacker->getAttack();

        if (rand(1,100) <= 20) {
            $damage *= 2;
        }

        return $defender->takeDamage($damage);
    }

    /**
     * Heilung
     */
    public static function heal(Fighter $fighter): int
    {
        $heal = rand(2,4);

        $fighter->heal($heal);

        return $heal;
    }

    /**
     * Schild
     */
    public static function shield(Fighter $fighter): int
    {
        $shield = 3;

        $fighter->addShield($shield);

        return $shield;
    }

    /**
     * Blitz
     */
    public static function lightning(Fighter $attacker, Fighter $defender): int
    {
        $damage = $attacker->getAttack() + 2;

        return $defender->takeDamage($damage);
    }

    /**
     * Legendäre Fähigkeit
     */
    public static function legendary(Fighter $attacker, Fighter $defender): int
    {
        $damage = $attacker->getAttack();

        if (rand(1,100) <= 50) {
            $damage += 8;
        }

        return $defender->takeDamage($damage);
    }

    /**
     * Führt die passende Fähigkeit aus.
     */
    public static function useAbility(Fighter $attacker, Fighter $defender): array
    {
        switch ($attacker->getAbility()) {

            case 'critical':

                return [
                    'type' => 'critical',
                    'value' => self::criticalAttack($attacker, $defender)
                ];

            case 'heal':

                return [
                    'type' => 'heal',
                    'value' => self::heal($attacker)
                ];

            case 'shield':

                return [
                    'type' => 'shield',
                    'value' => self::shield($attacker)
                ];

            case 'lightning':

                return [
                    'type' => 'lightning',
                    'value' => self::lightning($attacker, $defender)
                ];

            case 'legendary':

                return [
                    'type' => 'legendary',
                    'value' => self::legendary($attacker, $defender)
                ];

            default:

                return [
                    'type' => 'attack',
                    'value' => self::attack($attacker, $defender)
                ];
        }
    }
}
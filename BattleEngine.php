<?php

require_once __DIR__ . '/Fighter.php';
require_once __DIR__ . '/BattleFunctions.php';
require_once __DIR__ . '/BattleLogger.php';

class BattleEngine
{
    private BattleLogger $logger;

    public function __construct()
    {
        $this->logger = new BattleLogger();
    }

    public function fight(Fighter $creator, Fighter $opponent): array
    {
        $round = 1;

        while ($creator->isAlive() && $opponent->isAlive()) {

            // -----------------------------
            // Spieler 1 greift an
            // -----------------------------

            $result = BattleFunctions::useAbility($creator, $opponent);

            $this->logger->add(
                $round,
                $result["type"],
                $creator->getName(),
                $opponent->getName(),
                $result["value"],
                $opponent->getHp()
            );

            if (!$opponent->isAlive()) {

                break;
            }

            // -----------------------------
            // Spieler 2 greift an
            // -----------------------------

            $result = BattleFunctions::useAbility($opponent, $creator);

            $this->logger->add(
                $round,
                $result["type"],
                $opponent->getName(),
                $creator->getName(),
                $result["value"],
                $creator->getHp()
            );

            $round++;

            if ($round > 100) {
                break;
            }
        }

        return [

            "winner" => $creator->isAlive()
                ? "creator"
                : "opponent",

            "creator" => [

                "hp" => $creator->getHp(),

                "alive" => $creator->isAlive()
            ],

            "opponent" => [

                "hp" => $opponent->getHp(),

                "alive" => $opponent->isAlive()
            ],

            "log" => $this->logger->getLog()

        ];
    }
}
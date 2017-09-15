<?php

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class EyeOfEnder extends Item
{

    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::EYE_OF_ENDER, 0, $count, "Eye Of Ender");
    }

    public function canBeActivated(): bool
    {
        return true;
    }

    public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz)
    {
        if ($target->getId() == Block::END_PORTAL_FRAME and $player->getServer()->endEnabled) {
            if ($target->getDamage() !== 4) {
                $level->setBlock(new Vector3($target->getX(), $target->getY(), $target->getZ()), Block::get(Block::END_PORTAL_FRAME, 4));
                $x = $target->x;
                $y = $target->y;
                $z = $target->z;
                if ($level->getBlock(new Vector3($x - 1, $y, $z))->getDamage() == 4) {
                    if ($level->getBlock(new Vector3($x - 2, $y, $z))->getDamage() == 4 or $level->getBlock(new Vector3($x + 1, $y, $z))->getDamage() == 4) {
                        if ($player->getServer()->rowPositive == false) {
                            $player->getServer()->rowPositive = true;
                        } elseif ($player->getServer()->rowNegative == false) {
                            $player->getServer()->rowNegative = true;
                        }
                    }
                } elseif ($level->getBlock(new Vector3($x + 1, $y, $z))->getDamage() == 4) {
                    if ($level->getBlock(new Vector3($x + 2, $y, $z))->getDamage() == 4 or $level->getBlock(new Vector3($x - 1, $y, $z))->getDamage() == 4) {
                        if ($player->getServer()->rowPositive == false) {
                            $player->getServer()->rowPositive = true;
                        } elseif ($player->getServer()->rowNegative == false) {
                            $player->getServer()->rowNegative = true;
                        }
                    }
                } elseif ($level->getBlock(new Vector3($x, $y, $z - 1))->getDamage() == 4) {
                    if ($level->getBlock(new Vector3($x, $y, $z - 2))->getDamage() == 4 or $level->getBlock(new Vector3($x, $y, $z + 1))->getDamage() == 4) {
                        if ($player->getServer()->columPositive == false) {
                            $player->getServer()->columPositive = true;
                        } elseif ($player->getServer()->columNegative == false) {
                            $player->getServer()->columNegative = true;
                        }
                    }
                } elseif ($level->getBlock(new Vector3($x, $y, $z + 1))->getDamage() == 4) {
                    if ($level->getBlock(new Vector3($x, $y, $z + 2))->getDamage() == 4 or $level->getBlock(new Vector3($x, $y, $z - 1))->getDamage() == 4) {
                        if ($player->getServer()->columPositive == false) {
                            $player->getServer()->columPositive = true;
                        } elseif ($player->getServer()->columNegative == false) {
                            $player->getServer()->columNegative = true;
                        }
                    }
                }

                if ($player->getServer()->columPositive && $player->getServer()->columNegative && $player->getServer()->rowPositive && $player->getServer()->rowNegative) {
                    $player->getServer()->columPositive = false;
                    $player->getServer()->columNegative = false;
                    $player->getServer()->rowPositive = false;
                    $player->getServer()->rowNegative = false;

                    $sendCenter1 = [0, 0];
                    $sendCenter2 = [0, 0];
                    $correctCenterX = 0;
                    $correctCenterZ = 0;

                    if ($level->getBlock(new Vector3($x - 1, $y, $z))->getDamage() == 4 || $level->getBlock(new Vector3($x + 1, $y, $z))->getDamage() == 4) {
                        if ($level->getBlock(new Vector3($x - 2, $y, $z))->getDamage() == 4) {
                            $sendCenter1[0] = $x - 1;
                        } else if ($level->getBlock(new Vector3($x + 2, $y, $z))->getDamage() == 4) {
                            $sendCenter1[0] = $x + 1;
                        } else {
                            $sendCenter1[0] = $x;
                        }
                        $sendCenter1[1] = $z;

                        if ($level->getBlock(new Vector3($x, $y, $z - 4))->getDamage() == 4) {
                            $sendCenter2[1] = $z - 4;
                        } else if ($level->getBlock(new Vector3($z, $y, $z + 4))->getDamage() == 4) {
                            $sendCenter2[1] = $z + 4;
                        }

                        $correctCenterX = $sendCenter1[0];
                        $step2ToCenter = $sendCenter1[1] - $sendCenter2[1];
                        $step2ToCenter = $step2ToCenter / 2;
                        $correctCenterZ = $sendCenter2[1] + $step2ToCenter;
                    } else if ($level->getBlock(new Vector3($x, $y, $z - 1))->getDamage() == 4 || $level->getBlock(new Vector3($x, $y, $z + 1))->getDamage() == 4) {
                        if ($level->getBlock(new Vector3($x, $y, $z - 2))->getDamage() == 4) {
                            $sendCenter1[1] = $z - 1;
                        } else if ($level->getBlock(new Vector3($x, $y, $z + 2))->getDamage() == 4) {
                            $sendCenter1[1] = $z + 1;
                        } else {
                            $sendCenter1[1] = $z;
                        }
                        $sendCenter1[0] = $x;

                        if ($level->getBlock(new Vector3($x - 4, $y, $z))->getDamage() == 4) {
                            $sendCenter2[0] = $x - 4;
                        } else if ($level->getBlock(new Vector3($x + 4, $y, $z))->getDamage() == 4) {
                            $sendCenter2[0] = $x + 4;
                        }

                        $correctCenterZ = $sendCenter1[1];
                        $step2ToCenter = $sendCenter1[0] - $sendCenter2[0];
                        $step2ToCenter = $step2ToCenter / 2;
                        $correctCenterX = $sendCenter2[0] + $step2ToCenter;
                    }

                    for ($i = -1; $i <= 1; $i++) {
                        for ($j = -1; $j <= 1; $j++) {
                            $level->setBlock(new Vector3($correctCenterX + $i, $y, $correctCenterZ + $j), Block::get(Block::END_PORTAL));
                        }
                    }
                }
            }
        }
        return parent::onActivate($level, $player, $block, $target, $face, $fx, $fy, $fz);
    }

}
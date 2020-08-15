<?php

declare(strict_types = 1);

namespace hachkingtohach1\MirrorNc\task;

use pocketmine\utils\TextFormat;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\entity\Human;
use pocketmine\entity\object\ItemEntity;
use slapper\entities\SlapperEntity;
use hachkingtohach1\MirrorNc\MirrorNc;

class ProcessSystem extends Task {
	
	/** @var int $timeRestart*/
	public $timeRestart = 180;
	
	/** @var int $timesClear*/
	public $timesClear = 50;
	
	/** @var bool $restart*/
	private $restart = false;
	
	public function onRun(int $currentTick){
		$prefix = MirrorNc::getInstance()->prefix;
		$loadServer = MirrorNc::getInstance()->getLoadServer();
		$maxMemory = MirrorNc::getInstance()->maxMemoryToRestart();
		$memoryUsed = MirrorNc::getInstance()->getMemoryUsedServer();
		$maxEntitiesItem = MirrorNc::getInstance()->maxEntitiesItem;
		foreach(MirrorNc::getInstance()->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $entity){
			    $maxDistance = MirrorNc::MAX_DISTANCE;			
			    foreach($entity->getLevel()->getNearbyEntities($entity->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $entity) as $entities){
				    if($entities instanceof ItemEntity and $entity instanceof ItemEntity){
						$entities->teleport(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()));
					}
				}
                if(count($level->getEntities()) >= $maxEntitiesItem){			    
				    if($entity instanceof ItemEntity){
					    if($memoryUsed >= MirrorNc::MAX_MEMORY_TO_CLEAN or $loadServer >= MirrorNc::MAX_LOAD_SERVER){
						    if(in_array($this->timesClear, [1, 2, 3, 4, 5, 30, 50])){
						        foreach(MirrorNc::getInstance()->getServer()->getOnlinePlayers() as $player){
								    $player->sendPopup($prefix.TextFormat::YELLOW."We are cleaning up in ".TextFormat::RED.$this->timesClear.TextFormat::YELLOW." second(s)!");
								}
							}
						    if($this->timesClear === 0){
							    $entity->close();
							    $this->timesClear = MirrorNc::STANDARD_TIME;
						    }
						    $this->timesClear--;
					    }
					}
                    if($memoryUsed >= MirrorNc::MAX_MEMORY_TO_CLEAN or $loadServer >= MirrorNc::MAX_LOAD_SERVER){
						$countEntities = 0;
						if(!($entity instanceof Player) or !($entity instanceof Human)){
							$slapper = MirrorNc::getInstance()->getServer()->getPluginManager()->getPlugin('Slapper');
							if($slapper !== null){
								if($entity instanceof SlapperEntity){
									return;
							    }
							}
							$entity->close();
					    }
					}
				}
			}
		}			
		if($memoryUsed >= $maxMemory){
			$this->restart = true;
		}
        if($this->restart){
			foreach(MirrorNc::getInstance()->getServer()->getOnlinePlayers() as $player){
			    $player->sendTip($prefix.TextFormat::YELLOW."Restarting in ".TextFormat::RED.$this->timeRestart);
			}
			if($this->timeRestart === 0){
				MirrorNc::getInstance()->getServer()->shutdown();
			}
			$this->timeRestart--;
		}			
	}
}
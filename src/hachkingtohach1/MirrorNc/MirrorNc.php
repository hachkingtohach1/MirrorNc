<?php

declare(strict_types=1);

namespace hachkingtohach1\MirrorNc;

use pocketmine\utils\Process;
use pocketmine\plugin\PluginBase;
use hachkingtohach1\MirrorNc\task\ProcessSystem;

class MirrorNc extends PluginBase {
	
	CONST MAX_MEMORY_TO_CLEAN = 250;
	CONST MAX_LOAD_SERVER = 70;
	CONST MAX_DISTANCE = 2;
	CONST STANDARD_TIME = 100;
	
	/** @var null $instance*/
	private static $instance = null;
	
	/** @var int $maxMemory*/
	public $maxMemory = 4320;
	
	/** @var int $maxEntitiesItem*/
	public $maxEntitiesItem = 50;
	
	/** @var string $prefix*/
	public $prefix = "[MirrorNc] ";
	
	public function onLoad(): void{
        self::$instance = $this;
	}
	
	public static function getInstance(): MirrorNc{
        return self::$instance;
    }
	
	public function onEnable(){
		$this->getScheduler()->scheduleRepeatingTask(new ProcessSystem($this), 20);
	}
	
	public function onDisable(){}
	
	public function maxMemoryToRestart(){
		return $this->maxMemory;
	}
	
	public function getThreadCount() :int{
		$thread = Process::getThreadCount();
		return $thread;
	}	
	
	public function getLoadServer() :float{
		$load = $this->getServer()->getTickUsageAverage();
		return $load;
	}

    public function maxMemoryServer() :float{
		$u = Process::getAdvancedMemoryUsage();
		$max = round(($u[2] / 1024) / 1024, 2);
		return $max;
	}		

    public function getMemoryUsedServer() :float{
		$u = Process::getAdvancedMemoryUsage();
		$used = round(($u[0] / 1024) / 1024, 2);
		return $used;
    }	
}
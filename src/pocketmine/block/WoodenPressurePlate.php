<?php

/*
 *
 *  _                       _           _ __  __ _             
 * (_)                     (_)         | |  \/  (_)            
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___  
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \ 
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/ 
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___| 
 *                     __/ |                                   
 *                    |___/                                                                     
 * 
 * This program is a third party build by ImagicalMine.
 * 
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalcorp.ml/
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\item\Tool;
use pocketmine\entity\Entity;

class WoodenPressurePlate extends Transparent implements Redstone{

	protected $id = self::WOODEN_PRESSURE_PLATE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}
	
	public function hasEntityCollision(){
		return true;
	}

	public function getName(){
		return "Wooden Pressure Plate";
	}

	public function getHardness(){
		return 0.5;
	}

	public function getPower(){
		return $this->isPowered()?15:0;
	}
	
	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_SCHEDULED){
			if($this->meta == 1){
				$this->meta =0;
				$this->getLevel()->setBlock($this, Block::get($this->getId(), $this->meta), false, false, true);
				return Level::BLOCK_UPDATE_WEAK;
			}
		}
		return false;
	}

	public function onEntityCollide(Entity $entity){
		if($this->meta == 0){
			$this->meta = 1;
			$this->getLevel()->setBlock($this, $this, true , true);
		}
	}
	
	public function onEntityUnCollide(Entity $entity){
		if($this->meta === 1){
			$this->meta = 0;
			$this->getLevel()->setBlock($this, $this, true , true);
		}
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $target->getSide(Vector3::SIDE_DOWN);
		if($down->isTransparent() === false || $down instanceof Fence || $down instanceof FenceGate /*|| $down instanceof Stair || $down instanceof Slab*/){
			$this->getLevel()->setBlock($block, $this, true, true);
			
			return true;
		}
		
		return false;
	}
	
	public function onActivate(Item $item, Player $player=null){
		$this->togglePowered();
	}

	public function getDrops(Item $item){
		return [[$this->id,0,1]];
	}

	public function isPowered(){
		return (($this->meta & 0x01) === 0x01);
	}

	/**
	 * Toggles the current state of this button
	 *
	 * @param
	 *        	bool
	 *        	whether or not the button is powered
	 */
	public function togglePowered(){
		$this->meta ^= 0x01;
		$this->isPowered()?$this->power=15:$this->power=0;
		$this->getLevel()->setBlock($this, $this);
	}
}
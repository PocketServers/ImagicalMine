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
/*
 * THIS IS COPIED FROM THE PLUGIN FlowerPot MADE BY @beito123!!
 * https://github.com/beito123/PocketMine-MP-Plugins/blob/master/test%2FFlowerPot%2Fsrc%2Fbeito%2FFlowerPot%2Fomake%2FSkull.php
 * 
 */

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\tag\String;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\math\AxisAlignedBB;

class SkullBlock extends Transparent{

	protected $id = self::SKULL_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 1;
	}

	public function isSolid(){
		return false;
	}

	public function getBoundingBox(){ // todo fix
		return new AxisAlignedBB($this->x, $this->y, $this->z, $this->x + 0.75, $this->y + 0.5, $this->z + 0.75);
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($face !== 0){
			$this->getLevel()->setBlock($block, Block::get(Block::SKULL_BLOCK, 0), true, true);
			$nbt = new Compound("", [
				new String("id", Tile::SKULL),
				new Int("x", $block->x),
				new Int("y", $block->y),
				new Int("z", $block->z),
				new Byte("SkullType", $item->getDamage()),
				new Byte("Rot", floor(($player->yaw * 16 / 360) + 0.5) & 0x0F),
			]);
			$chunk = $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4);
			$pot = Tile::createTile("Skull", $chunk, $nbt);
			$this->getLevel()->setBlock($block, Block::get(Block::SKULL_BLOCK, $face), true, true);
			return true;
		}
		return false;
	}

	public function getResistance(){
		return 5;
	}

	public function getName(){
		static $names = [0 => "Skeleton Skull",1 => "Wither Skeleton Skull",2 => "Zombie Head",3 => "Head",4 => "Creeper Head"];
		return $names[$this->meta & 0x03];
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function onBreak(Item $item){
		$this->getLevel()->setBlock($this, new Air(), true, true, true);
		return true;
	}

	public function getDrops(Item $item){
		if(($tile = $this->getLevel()->getTile($this)) instanceof Skull){
			return [[Item::SKULL,$tile->getSkullType(),1]];
		}
		return [[Item::SKULL,0,1]];
	}
}
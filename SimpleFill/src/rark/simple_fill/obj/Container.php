<?php
declare(strict_types = 1);

namespace rark\simple_fill\obj;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use rark\simple_fill\utils\VectorUtils;

class Container{
	protected Vector3 $min;
	protected Vector3 $max;
	protected Vector3 $pointer;
	/** @var Block[] */
	protected array $blocks = [];

	public function __construct(Vector3 $min, Vector3 $max){
		VectorUtils::sortVector($min, $max);
		$this->min = $min;
		$this->max = $max;
		$this->pointer = $min;
	}

	public function getMin():Vector3{
		return $this->min;
	}

	public function getMax():Vector3{
		return $this->max;
	}

	public function resize(?Vector3 $new_min = null, ?Vector3 $new_max = null):void{
		$this->min = $new_min?? $this->min;
		$this->max = $new_max?? $this->max;
	}

	public function setPointer(Vector3 $pointer):void{
		if(!$this->isVectorInside($pointer)) throw new \InvalidArgumentException('pointer must be inside a container');
		$this->pointer = $pointer;
	}

	public function resetPointer():void{
		$this->pointer = $this->min;
	}

	public function getPointer():Vector3{
		return $this->pointer;
	}

	public function fill(int $id, int $meta, World $world):bool{
		/** @var BlockFactory $factory */
		$factory = BlockFactory::getInstance();

		try{
			$block = $factory->get($id, $meta);
			
			foreach($this->foreach() as $v){
				$b = clone $block;
				$b->position($world, (int) $v->x, (int) $v->y, (int) $v->z);
				$this->blocks[serialize($v)] = $b;
			}
			return true;

		}catch(\Exception){
			$this->blocks = [];
			return false;
		}
	}

	/**
	 * コンテナ内の全ての座標を返します
	 * @return \Generator<Vector3>
	 */
	public function foreach():\Generator{
		for($x = $this->max->x-$this->min->x; $x>=0; --$x){
			for($y = $this->max->y-$this->min->y; $y>=0; --$y){
				for($z = $this->max->z-$this->min->z; $z>=0; --$z){
					yield $this->min->add($x, $y, $z);
				}
			}
		}
	}

	public function isVectorInside(Vector3 $v) : bool{
		if($v->x <= $this->min->x or $v->x >= $this->max->x) return false;
		if($v->y <= $this->min->y or $v->y >= $this->max->y) return false;
		return $v->z > $this->min->z and $v->z < $this->max->z;
	}

	public function loadBlocks(World $world):void{
		foreach($this->foreach() as $v){
			$block =  $world->getBlock($v);
			$block->position($world, (int) $v->x, (int) $v->y, (int) $v->z);
			$this->blocks[serialize($v)] = $block;
		}
	}

	/** @return Block[] */
	public function getBlocks():array{
		return $this->blocks;
	}

	public function place():void{
		foreach($this->getBlocks() as $block){
			$block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
		}
	}
}
<?php

namespace BlockHorizons\BlockPets\pets;

abstract class WalkingPet extends BasePet {

	protected $jumpTicks = 0;

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		parent::onUpdate($currentTick);
		if($petOwner === null || $this->isRidden()) {
			return false;
		}

		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}
		} else {
			$this->motionY -= $this->gravity;
		}
		if($this->isCollidedHorizontally && $this->jumpTicks === 0) {
			$this->jump();
		}
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$x = $petOwner->x - $this->x;
		$y = $petOwner->y - $this->y;
		$z = $petOwner->z - $this->z;

		if($x * $x + $z * $z < 5) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();
		return true;
	}

	public function doRidingMovement($motionX, $motionZ) {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;

		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}
		} else {
			$this->motionY -= $this->gravity;
		}
		if(($this->isCollidedHorizontally || $this->getRider()->isCollidedHorizontally) && $this->jumpTicks === 0) {
			$this->jump();
		}
		$this->move($motionX, $this->motionY, $motionZ);
		$this->updateMovement();
	}

	protected function jump() {
		$this->motionY = $this->gravity * 8;
		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->jumpTicks = 3;
	}
}

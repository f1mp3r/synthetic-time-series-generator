<?php

namespace App\DataGenerator;

class Row
{
    /**
     * @var integer
     */
    private $index;

    /**
     * @var Number[]
     */
    private $numbers;

    /**
     * @var bool
     */
    private $isCrossed = false;

    /**
     * @var int
     */
    private $class = 0;

    public function __construct($numbers = [])
    {
        $this->numbers = $numbers;
    }

    /**
     * @param bool $isCrossed
     */
    public function setIsCrossed(bool $isCrossed)
    {
        $this->isCrossed = $isCrossed;
        foreach ($this->numbers as $number) {
            $number->setIsChecked(false);
        }
    }

    /**
     * @return bool
     */
    public function isCrossed(): bool
    {
        return $this->isCrossed;
    }

    public function addNumber(Number $number): Row
    {
        $this->numbers[] = $number;

        return $this;
    }

    /**
     * @param int $index
     * @return Row
     */
    public function setIndex($index): Row
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return bool|Number
     */
    public function getNextUncrossedNumber()
    {
        foreach ($this->numbers as $number) {
            if (!$number->isChecked()) {
                return $number;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getClass(): int
    {
        return $this->class;
    }

    /**
     * @param int $class
     * @return Row
     */
    public function setClass(int $class): Row
    {
        $this->class = $class;

        return $this;
    }
}
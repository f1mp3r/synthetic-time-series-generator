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

    public function addNumber(Number $number)
    {
        $this->numbers[] = $number;
    }

    /**
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    public function getNextUncrossedNumber()
    {
        foreach ($this->numbers as $number) {
            if (!$number->isChecked()) {
                return $number;
            }
        }

        return false;
    }
}
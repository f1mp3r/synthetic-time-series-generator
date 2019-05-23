<?php

namespace App\DataGenerator;

class Number
{
    private $isChecked = false;
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * @param bool $isChecked
     */
    public function setIsChecked(bool $isChecked)
    {
        $this->isChecked = $isChecked;
    }

    /**
     * @return bool
     */
    public function isChecked(): bool
    {
        return $this->isChecked;
    }

    /**
     * @return float
     */
    public function getNumber()
    {
        return $this->number;
    }
}

<?php

namespace App\DataGenerator;

class ClassNumbers
{
    /**
     * @var Row[]
     */
    private $rows;

    public function __construct($rows = [])
    {
        $this->rows = $rows;
    }

    /**
     * @param $index
     * @param bool $cross
     */
    public function setRowCross($index, bool $cross)
    {
        $this->rows[$index]->setIsCrossed($cross);
    }

    public function resetRowsStatus()
    {
        foreach ($this->rows as $row) {
            $row->setIsCrossed(false);
        }
    }

    /**
     * @return Row
     */
    public function getRandomUncrossedRow()
    {
        $uncrossed = [];
        foreach ($this->rows as $index => $row) {
            if (!$row->isCrossed()) {
                $uncrossed[] = $row;
            }
        }

        return $uncrossed ? $uncrossed[array_rand($uncrossed)] : null;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param Row[] $rows
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @param Row $row
     */
    public function addRow(Row $row)
    {
        $this->rows[] = $row;
    }
}
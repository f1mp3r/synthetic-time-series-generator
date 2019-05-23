<?php

namespace App\DataGenerator;

use Exception;

class Generator
{
    private const NUMBER_AVERAGE = 3.536001766706810;
    private const STANDARD_DEVIATION = 0.766421563905520;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var array
     */
    private $headers = ['#', 'Est. vol', 'Class', 'Random', 'OUT', 'NOV', 'DEZ', 'JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'Year'];

    /**
     * Generator constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @param float $randomNumber
     * @return array|null
     * @throws Exception
     */
    private function generateRandomDataRow(float $randomNumber): ?array
    {
        $normSInv = Util::NormSInv($randomNumber);
        $thirdNumber = self::NUMBER_AVERAGE + self::STANDARD_DEVIATION * $normSInv;
        $exponential = exp($thirdNumber);
        $this->debug([
            'random' => $randomNumber,
            'normSInv' => $normSInv,
            'thirdNumber' => $thirdNumber,
            'exponential' => $exponential
        ]);

        $classId = Util::getClassIndex($exponential, $this->table->getClassification());
        $randomRow = $this->table->getRandomUncrossedRow($classId);

        if (!$randomRow) {
            $this->table->resetRowsStatus($classId);
            $randomRow = $this->table->getRandomUncrossedRow($classId);
        }

        $result = [];
        $result[] = $exponential;
        $result[] = $classId + 1;
        $result[] = $randomRow->getIndex() + 1;
        $this->debug($exponential, $randomRow->getIndex());

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $number = $randomRow->getNextUncrossedNumber();
            $number->setIsChecked(true);
            $generated = ($number->getNumber() * $exponential);
            $sum += $generated;
            $result[] = $generated;
        }

        $randomRow->setIsCrossed(true);
        $result[] = $sum;

        return $result;
    }

    public function generateTables(int $numberOfTables = 1, int $numberOfRows = 20, array $presetRandomNumbers = [])
    {
        $result = [];

        foreach (range(1, $numberOfTables) as $tableIndex) {
            $table = $this->generateTable(
                $numberOfRows,
                $presetRandomNumbers[$tableIndex - 1]
            );
            $table[] = [];
            $table[] = [];

            $result = array_merge(
                $result,
                $table
            );
        }

        return $result;
    }

    /**
     * @param int $numberOfRows
     * @param array $presetRandomNumbers
     * @param bool $withHeader
     * @return array
     * @throws Exception
     */
    public function generateTable(int $numberOfRows = 20, ?array $presetRandomNumbers = []): array
    {
        $table = [
            $this->headers
        ];

        foreach (range(1, $numberOfRows) as $index => $item) {
            $row = $this->generateRandomDataRow(
                $presetRandomNumbers[$index] ?? $this->generateRandomNumber()
            );

            array_unshift($row, $index + 1);
            $table[] = $row;
        }

        return $table;
    }

    /**
     * @param mixed ...$args
     */
    private function debug(...$args)
    {
        if ($this->debug) {
            dump($args);
        }
    }

    /**
     * @return float|int
     */
    private function generateRandomNumber()
    {
        return mt_rand() / mt_getrandmax();
    }
}
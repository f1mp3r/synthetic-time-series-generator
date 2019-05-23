<?php

namespace App\DataGenerator;

use Exception;

class Generator
{
    private $dataTable;
    private const NUMBER_AVERAGE = 3.536001766706810;
    private const STANDARD_DEVIATION = 0.766421563905520;
    private $debug = false;
    private $headers = ['', 'Est. vol', 'Class', 'Random', 'OUT', 'NOV', 'DEZ', 'JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'Year'];

    /**
     * Generator constructor.
     * @param Table $dataTable
     */
    public function __construct(Table $dataTable)
    {
        $this->dataTable = $dataTable;
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

        $categoryId = Util::getClassIndex($exponential);
        $randomRow = $this->dataTable->getClass($categoryId)->getRandomUncrossedRow();

        if (!$randomRow) {
            $this->dataTable->getClass($categoryId)->resetRowsStatus();
            $randomRow = $this->dataTable->getClass($categoryId)->getRandomUncrossedRow();
        }

        $csvData = [];
        $csvData[] = $exponential;
        $csvData[] = $categoryId + 1;
        $csvData[] = $randomRow->getIndex() + 1;
        $this->debug($exponential, $randomRow->getIndex());

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $number = $randomRow->getNextUncrossedNumber();
            $number->setIsChecked(true);
            $generated = ($number->getNumber() * $exponential);
            $sum += $generated;
            $csvData[] = $generated;
        }

        $randomRow->setIsCrossed(true);
        $csvData[] = $sum;

        return $csvData;
    }

    /**
     * @param int $numberOfRows
     * @param array $presetRandomNumbers
     * @return array
     * @throws Exception
     */
    public function generateTable(int $numberOfRows = 20, array $presetRandomNumbers = []): array
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
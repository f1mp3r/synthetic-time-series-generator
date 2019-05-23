<?php

namespace App\DataGenerator;

class Table
{
    /**
     * @var ClassNumbers[]
     */
    private $classes = [];

    public function __construct($classes = [])
    {
        $this->classes = $classes;
    }

    /**
     * @param array $dataTable
     * @return Table
     */
    public static function fromArray(array $dataTable): Table
    {
        $instance = new self();

        foreach ($dataTable as $i => $category) {
            $classCategory = new ClassNumbers();

            foreach ($category as $index => $rowNumbers) {
                $rowObject = new Row();
                $rowObject->setIndex($index);

                foreach ($rowNumbers as $number) {
                    $rowObject->addNumber(new Number($number));
                }

                $classCategory->addRow($rowObject);
            }

            $instance->addClass($classCategory);
        }

        return $instance;
    }

    public static function fromExternal($dataTable, $classification)
    {
        $instance = new self;

        self::performTableValidations($dataTable);
        self::performClassificationValidations($classification);
    }

    private static function performClassificationValidations($classification)
    {
    }

    private static function performTableValidations($dataTable)
    {
        $csvRows = array_map('str_getcsv', explode("\r\n", trim($dataTable)));
        dump($csvRows);
        die;

        if (!is_array($csvRows)) {
            throw new \Exception('Data table provided is not valid');
        }

        foreach ($csvRows as $csvRow) {

        }
    }

    /**
     * @return ClassNumbers[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param ClassNumbers[] $classes
     */
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
    }

    public function addClass(ClassNumbers $classNumbers)
    {
        $this->classes[] = $classNumbers;
    }

    public function getClass(int $index): ClassNumbers
    {
        return $this->classes[$index];
    }
}
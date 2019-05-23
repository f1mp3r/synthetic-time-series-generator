<?php

namespace App\DataExporter\Contract;

interface FormatInterface
{
    public static function format(array $data = [], array $options = []): ?string;
}
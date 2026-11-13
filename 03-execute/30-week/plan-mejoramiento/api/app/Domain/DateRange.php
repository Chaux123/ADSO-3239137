
<?php

namespace App\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final class DateRange
{
    public static function validate(string $from, string $to): void
    {
        $start = self::parseDate($from);
        $end = self::parseDate($to);

        if ($end < $start) {
            throw new InvalidArgumentException(
                'La fecha final no puede ser anterior a la inicial.'
            );
        }
    }

    private static function parseDate(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $value,
            new \DateTimeZone('UTC')
        );

        if (
            $date === false ||
            $date->format('Y-m-d') !== $value
        ) {
            throw new InvalidArgumentException(
                'La fecha debe tener el formato YYYY-MM-DD.'
            );
        }

        return $date;
    }
}

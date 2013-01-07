<?php

// Convert a date of a particular format into one that mysql can use
function getGoodDate($badFormat = 'd/m/y', DateTime $badDate)
{
    return DateTime::createFromFormat($badFormat, $badDate)->format('Y-m-d');
}


/**
 * @param int $start            The value to start from (inclusive)
 * @param int $end              The value to end at (inclusive)
 * @param int $padding          Pad with zeros to this length
 * @param int $paddingDirection Which direction to pad values
 * @param int $step             The number to increment by
 */
function getSelectValues($start, $end, $padding = 2, $paddingDirection = STR_PAD_LEFT, $step = 1)
{
    $start = (int) $start;
    $end = (int) $end;
    for ($i = $start; $i <= $end; $i += $step) {
        $value = str_pad($i, $padding, '0', $paddingDirection);
        // Display = str_pad($value, 4, '20', STR_PAD_LEFT);
        echo "<option value='$value'>$value</option>\n";
    }
}
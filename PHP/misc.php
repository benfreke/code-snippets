<?php

/**
 * Returns a nicely formatted date into one usable universally across programs
 * @param string   $badFormat The format of the badly formatted date
 * @param string   $badDate   The datetime string
 *
 * @return string
 */
function getGoodDate($badFormat = 'd/m/y', $badDate)
{
    return DateTime::createFromFormat($badFormat, $badDate)->format('Y-m-d');
}


/**
 * Create the options for a select box, particularly handy for expire fields
 * @param int $start            The value to start from (inclusive)
 * @param int $end              The value to end at (inclusive)
 * @param int $padding          Pad with zeros to this length
 * @param int $paddingDirection Which direction to pad values
 * @param int $step             The number to increment by
 */
function getSelectValues($start, $end, $padding = 2, $paddingDirection = STR_PAD_LEFT, $step = 1)
{
    $start = (int) $start;
    $end   = (int) $end;
    for ($i = $start; $i <= $end; $i += $step) {
        $value = str_pad($i, $padding, '0', $paddingDirection);
        // Display = str_pad($value, 4, '20', STR_PAD_LEFT);
        echo "<option value='$value'>$value</option>\n";
    }
}
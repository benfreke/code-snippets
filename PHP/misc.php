<?php

// Convert a date of a particular format into one that mysql can use
$goodFormatDate=  DateTime::createFromFormat('d/m/y', $badFormatDate)->format('Y-m-d');
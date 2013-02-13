<?php

/**
 * A simple class to test how long functions take
 */
class benTester
{
    public $iterations = 10000;
    public $name = '';
    public $start = 0;
    public $end = 0;

    /**
     * @param string $name The name of the test
     * @param int $iterations
     */
    public function __construct($name, $iterations = NULL)
    {
        $this->name = $name;
        if ($iterations) {
            $this->iterations = $iterations;
        }
    }

    public function runTest()
    {
        $this->start = microtime(TRUE);
        for ($i = 0; $i < $this->iterations; $i++) {
            $this->test();
        }
        $this->end = microtime(TRUE);
    }

    public function getTime()
    {
        return $this->end - $this->start;
    }

    public function result($compare = null)
    {
        $result = "{$this->name} took: {$this->getTime()}";
        if (!empty($compare) && is_float($compare)) {
            $result .= ", which was ";
            $ourTime = $this->getTime();
            if ($ourTime > $compare) {
                $result .= round(($ourTime / $compare) * 100, 2) . "% slower";
            } elseif ($ourTime === $compare) {
                " the same";
            } elseif ($compare > $ourTime) {
                $result .= round(($compare / $ourTime) * 100, 2) . "% <strong>faster</strong>";
            } else {
                $result .= ' incalculable';
            }
            $result .= '<br />';

        }
        return $result;
    }
}

class arrayTest extends benTester
{
    public $test = array(
        'first',
        'second',
        'third'
    );

    public function test()
    {
        array_push($this->test, 'fourth');
        $value = implode('_', $this->test);
        array_pop($this->test);
        return $value;
    }
}

class stringTest extends benTester
{
    public $test = 'first_second_third';

    public function test()
    {
        $value = $this->test;
        $value .= "_" . "fourth";
        return $value;
    }
}

$array = new arrayTest('Array');
$string = new stringTest('String');

$array->runTest();
$string->runTest();

echo $array->result($string->getTime());

echo $string->result($array->getTime());
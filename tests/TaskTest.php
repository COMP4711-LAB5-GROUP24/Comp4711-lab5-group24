<?php
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{

    /**
     * @dataProvider TaskDataProvider
     */
    public function testSetTask($case, $property, $input, $expected)
    {
        $taskEntity = new Entity;
        $taskEntity->$property = $input;
        $this->assertEquals($expected, $taskEntity->$property == $input);
    }

    public function TaskDataProvider()
    {
        $testdata = json_decode(file_get_contents("testdata/set_task_data.json"),true);
        return $testdata;
    }
}
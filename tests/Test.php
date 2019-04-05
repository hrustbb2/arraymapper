<?php

namespace hrustbb2\arraymapper\tests;

use PHPUnit\Framework\TestCase;
use hrustbb2\arraymapper\ArrayMapper;

class ArrayMapperTest extends TestCase
{
    public function testMap()
    {
        $data = [
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 2, 'city_name' => 'cityName2', 'house_id' => 3, 'house_name' => 'hn3'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 1, 'city_name' => 'cityName1', 'house_id' => 1, 'house_name' => 'hn1'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 1, 'city_name' => 'cityName1', 'house_id' => 2, 'house_name' => 'hn2'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 2, 'city_name' => 'cityName2', 'house_id' => 4, 'house_name' => 'hn4'],
        ];

        $housesMapper = new ArrayMapper('house_');
        $cityesMapper = new ArrayMapper('city_', ['houses' => $housesMapper]);
        $dataMapper = new ArrayMapper('m_', ['cityes' => $cityesMapper]);

        $parsed = $dataMapper->map($data);

        $this->assertEquals('hn3', $parsed[1]['cityes'][2]['houses'][3]['name']);
    }

    public function testKeys()
    {
        $data = [
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 1, 'city_name' => 'cityName1', 'house_one' => 1, 'house_two' => 1, 'house_name' => 'hn1'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 1, 'city_name' => 'cityName1', 'house_one' => 2, 'house_two' => 1, 'house_name' => 'hn2'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 2, 'city_name' => 'cityName2', 'house_one' => 3, 'house_two' => 1, 'house_name' => 'hn3'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 2, 'city_name' => 'cityName2', 'house_one' => 4, 'house_two' => 1, 'house_name' => 'hn4'],
        ];

        $housesMapper = new ArrayMapper('house_', [], ['one', 'two']);
        $cityesMapper = new ArrayMapper('city_', ['houses' => $housesMapper]);
        $dataMapper = new ArrayMapper('m_', ['cityes' => $cityesMapper]);

        $parsed = $dataMapper->map($data);

        $this->assertEquals('hn3', $parsed[1]['cityes'][2]['houses']['3.1']['name']);
    }

    public function testBuild()
    {
        $data = [
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 1, 'city_name' => 'cityName1', 'house_one' => 1, 'house_two' => 1, 'house_name' => 'hn1'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 1, 'city_name' => 'cityName1', 'house_one' => 2, 'house_two' => 1, 'house_name' => 'hn2'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 2, 'city_name' => 'cityName2', 'house_one' => 3, 'house_two' => 1, 'house_name' => 'hn3'],
            ['m_id' => 1, 'm_name' => 'n1', 'city_id' => 2, 'city_name' => 'cityName2', 'house_one' => 4, 'house_two' => 1, 'house_name' => 'hn4'],
        ];

        $config = [
            'prefix' => 'm_',
            'cityes' => [
                'prefix' => 'city_',
                'houses' => [
                    'prefix' => 'house_',
                    'primaryKeys' => ['one', 'two']
                ]
            ]
        ];

        $mapper = ArrayMapper::build($config);
        $parsed = $mapper->map($data);

        $this->assertEquals('hn3', $parsed[1]['cityes'][2]['houses']['3.1']['name']);
    }
}

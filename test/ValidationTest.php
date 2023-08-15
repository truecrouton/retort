<?php

use PHPUnit\Framework\TestCase;
use Retort\Test\Helper\Jacket;
use Retort\Validation\Validation;

class ValidationTest extends TestCase
{
    public function testValidation()
    {
        $jacketReq = [
            'description' => 'Supertex Jacket',
            'price' => 59,
            'manufacturer' => [
                'name' => 'Exclusive Inc.'
            ],
            'pockets' => [
                [
                    'location' => 'front',
                    'hasZipper' => 0
                ],
                [
                    'location' => 'interior',
                    'hasZipper' => 1
                ]
            ]
        ];

        $jacket = Validation::createObject(Jacket::class, $jacketReq);

        $this->assertTrue($jacket instanceof Jacket);
        $this->assertEquals($jacketReq['description'], $jacket->description);
        $this->assertEquals($jacketReq['price'], $jacket->price);

        $this->assertObjectHasProperty('manufacturer', $jacket);
        $this->assertEquals($jacketReq['manufacturer']['name'], $jacket->manufacturer->name);

        $this->assertIsArray($jacket->pockets);
        $this->assertCount(2, $jacket->pockets);

        $pocket1 = $jacket->pockets[0];
        $this->assertEquals($jacketReq['pockets'][0]['location'], $pocket1->location);
        $this->assertEquals($jacketReq['pockets'][0]['hasZipper'], $pocket1->hasZipper);

        $pocket2 = $jacket->pockets[1];
        $this->assertEquals($jacketReq['pockets'][1]['location'], $pocket2->location);
        $this->assertEquals($jacketReq['pockets'][1]['hasZipper'], $pocket2->hasZipper);

        $jacketReq['manufacturer']['address'] = 'Las Vegas, NV';

        $jacket = Validation::createObject(Jacket::class, $jacketReq);
        $this->assertObjectHasProperty('manufacturer', $jacket);
        $this->assertEquals($jacketReq['manufacturer']['address'], $jacket->manufacturer->address);
    }

    public function testValidationException()
    {
        $jacketReq = [
            'description' => 'Supertex Jacket',
            'price' => 5,
            'manufacturer' => [
                'name' => 'Exclusive Inc.'
            ],
            'pockets' => [
                [
                    'location' => 'front',
                    'hasZipper' => 0
                ]
            ]
        ];

        $this->expectExceptionMessage("'5' is an invalid number for price.");
        Validation::createObject(Jacket::class, $jacketReq);
    }
}

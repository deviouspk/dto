<?php

namespace DtoTest\UseCases;

use Cake\Chronos\Chronos;
use Carbon\Carbon;
use Dto\Dto;
use Dto\DtoInterface;
use Dto\Exceptions\InvalidCarbonValueException;
use DtoTest\TestCase;

class SimpleTest extends TestCase
{
    public function testConstructor()
    {
        $dto = new Dto(['foo' => 'bar']);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $this->assertEquals(['foo' => 'bar'], $dto->toArray());
    }

    public function testHydrateWithObject()
    {
        $dto = new Dto();
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->hydrate(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $dto->toArray());
    }

    public function testHydrateWithArray()
    {
        $dto = new Dto();
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->hydrate(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $dto->toArray());
    }


    public function testHydrateWillPerformTypecasting()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'i' => ['type' => 'integer']
            ],
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->hydrate(['a' => 'apple', 'i' => '42']);
        $this->assertEquals(['a' => 'apple', 'i' => 42], $dto->toArray());

        $dto->set('a', 'amazing');

        $this->assertEquals(['a' => 'amazing', 'i' => 42], $dto->toArray());
    }

    public function testSetAutomaticallyDeepensStructure()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'i' => ['type' => 'integer']
            ],
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->set('a', 'amazing');

        $this->assertEquals(['a' => 'amazing'], $dto->toArray());

    }

    public function testSetUsingObjectNotationAutomaticallyDeepensStructure()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'i' => ['type' => 'integer']
            ],
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->a = 'amazing';

        $this->assertEquals(['a' => 'amazing'], $dto->toArray());
    }


    public function testSimpleArray()
    {
        $schema = [
            'type' => 'array',
            'items' => ['type' => 'string']
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto[] = 'amazing';

        $this->assertEquals(['amazing'], $dto->toArray());
    }

    public function testNonNullableStringsGetSetToEmptyString()
    {
        $dto = new X();

        $dto->name = 'Lars';
        $dto->email = 'some@email.com';
        $dto->email = null;

        $this->assertEquals('', $dto->email->toScalar());
        $this->assertEquals('', strval($dto->email));
    }

    public function testIntegersAreReturnedAsIntegers()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'properties' => [
                'i' => ['type' => 'integer']
            ]
        ]);
        $dto->i = 5;
        $this->assertEquals(5, strval($dto->i));

    }

    public function testX()
    {
        $data = [
            'x' => 'xray'
        ];
        $schema = null;
        $schema = [
            'type' => 'object'
        ];

        $dto = new Dto($data, $schema);

        $this->assertEquals('xray', $dto->x);

    }

    /**
     * @test
     * @group timestamp
     */
    public function testCarbon()
    {

        $time = Carbon::now();
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => 'timestamp'
                ]
            ]
        ];

        $dto = new Dto($data, $schema);

        $this->assertEquals($time, $dto->get('time'));

    }

    /**
     * @test
     * @group timestamp
     */
    public function testChronos()
    {

        $time = Chronos::now();
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => 'timestamp'
                ]
            ]
        ];

        $dto = new Dto($data, $schema);

        $this->assertEquals($time, $dto->get('time'));

    }

    /**
     * @test
     * @group timestamp
     */
    public function testStringTimestamp()
    {
        $time = "2018-08-17 13:50:45.150374";
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => 'timestamp'
                ]
            ]
        ];

        $dto = new Dto($data, $schema);
        $this->assertEquals(new Carbon($time), $dto->get('time'));
    }

    /**
     * @test
     * @group timestamp
     * @group timestampArray
     */
    public function testTimestampToArray()
    {
        $time = "2018-08-17 13:50:45.150374";
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => [
                        'null',
                        'timestamp'
                    ]
                ]
            ]
        ];

        $dto = new Dto($data, $schema);

        $array = ($dto->toArray());
        $this->assertEquals(new Carbon($time), $array['time']);
    }

    /**
     * @test
     * @group timestamp
     * @group timestampNull
     */
    public function testStringTimestampWithNull()
    {
        $time = "2018-08-17 13:50:45.150374";
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => [
                        'timestamp',
                        'null',
                    ]
                ]
            ]
        ];

        $dto = new Dto($data, $schema);
        $this->assertEquals(new Carbon($time), $dto->time);
    }

    /**
     * @test
     * @group timestamp
     * @group timestampNull
     */
    public function testCarbonTimestampWithNull()
    {
        $time = Carbon::now();
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => [
                        'timestamp',
                        'null'
                    ]
                ]
            ]
        ];

        $dto = new Dto($data, $schema);
        $this->assertEquals($time, $dto->time);
    }

    /**
     * @test
     * @group timestamp
     * @group timestampNull
     */
    public function testNullTimestampWithNull()
    {
        $time = null;
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => [
                        'null',
                        'timestamp',

                    ]
                ]
            ]
        ];

        $dto = new Dto($data, $schema);

        $this->assertEquals($time, $dto->time);
    }

    /**
     * @test
     * @group null
     */
    public function testNull()
    {
        $time = null;
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => [
                        'null'
                    ]
                ]
            ]
        ];

        $dto = new Dto($data, $schema);

        $this->assertEquals($time, $dto->time);
    }

    /**
     * @test
     * @group null
     */
    public function testStringNullTimestamp()
    {
        $input = 'hello';
        $input2 = "2018-08-17 13:50:45.150374";
        $input3 = null;
        $input4 = Carbon::now();
        $input5 = "2018-0:45.150374";

        $data = [
            'time' => $input
        ];
        $data2 = [
            'time' => $input2
        ];
        $data3 = [
            'time' => $input3
        ];
        $data4 = [
            'time' => $input4
        ];
        $data5 = [
            'time' => $input5
        ];

        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => [
                        'null',
                        'timestamp',
                        'string'
                    ]
                ]
            ]
        ];

        $dto = new Dto($data, $schema);
        $dto2 = new Dto($data2, $schema);
        $dto3 = new Dto($data3, $schema);
        $dto4 = new Dto($data4, $schema);
        $dto5 = new Dto($data5, $schema);


        $this->assertEquals($input, $dto->time);
        $this->assertEquals(new Carbon($input2), $dto2->time);
        $this->assertEquals($input3, $dto3->time);
        $this->assertEquals($input4, $dto4->time);
        $this->assertEquals($input5, $dto5->time);
    }

    /**
     * @test
     * @group timestamp
     * @expectedException \Dto\Exceptions\InvalidCarbonValueException
     */
    public function testWrongTimestamp()
    {
        $time = 254;
        $data = [
            'time' => $time
        ];
        $schema = null;
        $schema = [
            'type' => 'object',
            'properties' => [
                'time' => [
                    'type' => 'timestamp'
                ]
            ]
        ];

        new Dto($data, $schema);
    }
}

class X extends Dto
{
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'email' => ['type' => 'string'],
        ]
    ];
}
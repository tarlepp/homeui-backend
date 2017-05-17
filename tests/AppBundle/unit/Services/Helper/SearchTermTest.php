<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/unit/Services/Helper/SearchTermTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\unit\Services\Helper;

use App\Services\Helper\SearchTerm;
use App\Tests\KernelTestCase;

/**
 * Class SearchTermTest
 *
 * @package Tests\AppBundle\Utils\Helpers
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class SearchTermTest extends KernelTestCase
{
    /**
     * @var SearchTerm
     */
    protected static $service;

    /**
     * @dataProvider dataProviderTestThatWithoutColumnOrSearchTermCriteriaIsNull
     *
     * @param   mixed   $column
     * @param   mixed   $search
     */
    public function testThatWithoutColumnOrSearchTermCriteriaIsNull($column, $search)
    {
        static::assertNull(SearchTerm::getCriteria($column, $search), 'Criteria was not NULL with given parameters');
    }

    /**
     * @dataProvider dataProviderTestThatReturnedCriteriaIsExpected
     *
     * @param array $inputArguments
     * @param array $expected
     */
    public function testThatReturnedCriteriaIsExpected(array $inputArguments, array $expected)
    {
        static::assertSame($expected, \call_user_func_array([SearchTerm::class, 'getCriteria'], $inputArguments));
    }

    /**
     * Data provider for testThatWithoutColumnOrSearchTermCriteriaIsNull
     *
     * @return array
     */
    public function dataProviderTestThatWithoutColumnOrSearchTermCriteriaIsNull(): array
    {
        return [
            [null, null],
            ['foo', null],
            [null, 'foo'],
            ['', ''],
            [' ', ''],
            ['', ' '],
            [' ', ' '],
            ['foo', ''],
            ['foo', ' '],
            ['', 'foo'],
            [' ', 'foo'],
            [[], []],
            [[null], [null]],
            [['foo'], [null]],
            [[null], ['foo']],
            [[''], ['']],
            [[' '], ['']],
            [[''], [' ']],
            [[' '], [' ']],
            [['foo'], ['']],
            [['foo'], [' ']],
            [[''], ['foo']],
            [[' '], ['foo']],
        ];
    }

    /**
     * Data provider for testThatReturnedCriteriaIsExpected
     *
     * @return array
     */
    public function dataProviderTestThatReturnedCriteriaIsExpected(): array
    {
        return [
            [
                ['c1', 'word'],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                [['c1', 'c2'], ['search', 'word']],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['entity.c2', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                            ['entity.c2', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                [['c1', 'c2'], 'search word'],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['entity.c2', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                            ['entity.c2', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                ['someTable.c1', 'search word'],
                [
                    'and' => [
                        'or' => [
                            ['someTable.c1', 'like', '%search%'],
                            ['someTable.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                ['someTable.c1', ['search', 'word']],
                [
                    'and' => [
                        'or' => [
                            ['someTable.c1', 'like', '%search%'],
                            ['someTable.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                [['c1', 'someTable.c1'], 'search word'],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['someTable.c1', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                            ['someTable.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                [['c1', 'someTable.c1'], ['search', 'word']],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['someTable.c1', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                            ['someTable.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                ['c1', 'search word', SearchTerm::OPERAND_AND],
                [
                    'and' => [
                        'and' => [
                            ['entity.c1', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                ['c1', 'search word', SearchTerm::OPERAND_OR],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                ['c1', 'search word', 'notSupportedOperand'],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                ['c1', 'search word', SearchTerm::OPERAND_OR, SearchTerm::MODE_FULL],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
            [
                ['c1', 'search word', SearchTerm::OPERAND_OR, SearchTerm::MODE_STARTS_WITH],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', 'search%'],
                            ['entity.c1', 'like', 'word%'],
                        ],
                    ],
                ],
            ],
            [
                ['c1', 'search word', SearchTerm::OPERAND_OR, SearchTerm::MODE_ENDS_WITH],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search'],
                            ['entity.c1', 'like', '%word'],
                        ],
                    ],
                ],
            ],
            [
                ['c1', 'search word', SearchTerm::OPERAND_OR, 666],
                [
                    'and' => [
                        'or' => [
                            ['entity.c1', 'like', '%search%'],
                            ['entity.c1', 'like', '%word%'],
                        ],
                    ],
                ],
            ],
        ];
    }
}

<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/integration/Services/Rest/Helper/RequestTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\integration\Services\Rest\Helper;

use App\Services\Rest\Helper\Request as RequestHelper;
use App\Tests\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestTest
 *
 * @package AppBundle\integration\Services\Rest\Helper
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class RequestTest extends KernelTestCase
{
    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Current 'where' parameter is not valid JSON.
     */
    public function testThatGetCriteriaMethodThrowsAnExceptionWithInvalidWhereParameter()
    {
        $fakeRequest = Request::create('/', 'GET', ['where' => '{foo bar']);

        RequestHelper::getCriteria($fakeRequest);
    }

    /**
     * @dataProvider dataProviderTestThatGetOrderByReturnsExpectedValue
     *
     * @param array $parameters
     * @param array $expected
     */
    public function testThatGetOrderByReturnsExpectedValue(array $parameters, array $expected)
    {
        $fakeRequest = Request::create('/', 'GET', $parameters);

        static::assertSame(
            $expected,
            RequestHelper::getOrderBy($fakeRequest),
            'getOrderBy method did not return expected value'
        );
    }

    public function testThatGetLimitReturnsNullWithoutParameter()
    {
        $fakeRequest = Request::create('/', 'GET');

        static::assertNull(
            RequestHelper::getLimit($fakeRequest),
            'getLimit method did not return NULL as it should without any parameters'
        );
    }

    /**
     * @dataProvider dataProviderTestThatGetLimitReturnsExpectedValue
     *
     * @param   array   $parameters
     * @param   integer $expected
     */
    public function testThatGetLimitReturnsExpectedValue(array $parameters, int $expected)
    {
        $fakeRequest = Request::create('/', 'GET', $parameters);

        $actual = RequestHelper::getLimit($fakeRequest);

        static::assertNotNull(
            $actual,
            'getLimit returned NULL and it should return an integer'
        );

        static::assertSame(
            $expected,
            $actual,
            'getLimit method did not return expected value'
        );
    }

    public function testThatGetOffsetReturnsNullWithoutParameter()
    {
        $fakeRequest = Request::create('/', 'GET');

        static::assertNull(
            RequestHelper::getOffset($fakeRequest),
            'getOffset method did not return NULL as it should without any parameters'
        );
    }

    /**
     * @dataProvider dataProviderTestThatGetOffsetReturnsExpectedValue
     *
     * @param   array   $parameters
     * @param   integer $expected
     */
    public function testThatGetOffsetReturnsExpectedValue(array $parameters, int $expected)
    {
        $fakeRequest = Request::create('/', 'GET', $parameters);

        $actual = RequestHelper::getOffset($fakeRequest);

        static::assertNotNull(
            $actual,
            'getOffset returned NULL and it should return an integer'
        );

        static::assertSame(
            $expected,
            $actual,
            'getOffset method did not return expected value'
        );
    }

    public function testThatGetSearchTermsReturnsEmptyArrayWithoutParameters()
    {
        $fakeRequest = Request::create('/', 'GET');

        static::assertSame(
            [],
            RequestHelper::getSearchTerms($fakeRequest),
            'getSearchTerms method did not return empty array ([]) as it should without any parameters'
        );
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Given search parameter is not valid, within JSON provide 'and' and/or 'or' property.
     */
    public function testThatGetSearchTermsThrowsAnExceptionWithInvalidJson()
    {
        $parameters = [
            'search' => '{"foo": "bar"}'
        ];

        $fakeRequest = Request::create('/', 'GET', $parameters);

        RequestHelper::getSearchTerms($fakeRequest);
    }

    /**
     * @dataProvider dataProviderTestThatGetSearchTermsReturnsExpectedValue
     *
     * @param   array   $expected
     * @param   string  $search
     */
    public function testThatGetSearchTermsReturnsExpectedValue(array $expected, string $search)
    {
        $parameters = [
            'search' => $search,
        ];

        $fakeRequest = Request::create('/', 'GET', $parameters);

        static::assertSame(
            $expected,
            RequestHelper::getSearchTerms($fakeRequest),
            'getSearchTerms method did not return expected value'
        );
    }

    /**
     * Data provider method for 'testThatGetOrderByReturnsExpectedValue' test.
     *
     * @return array
     */
    public function dataProviderTestThatGetOrderByReturnsExpectedValue(): array
    {
        return [
            [
                ['order' => 'column1'],
                ['column1' => 'ASC'],
            ],
            [
                ['order' => '-column1'],
                ['column1' => 'DESC'],
            ],
            [
                ['order' => 't.column1'],
                ['t.column1' => 'ASC'],
            ],
            [
                ['order' => '-t.column1'],
                ['t.column1' => 'DESC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'ASC',
                    ],
                ],
                ['column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'DESC',
                    ],
                ],
                ['column1' => 'DESC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'foobar',
                    ],
                ],
                ['column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'ASC',
                    ],
                ],
                ['t.column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'DESC',
                    ],
                ],
                ['t.column1' => 'DESC'],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'foobar',
                    ],
                ],
                ['t.column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'ASC',
                        'column2' => 'DESC',
                    ],
                ],
                [
                    'column1' => 'ASC',
                    'column2' => 'DESC',
                ],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'ASC',
                        't.column2' => 'DESC',
                    ],
                ],
                [
                    't.column1' => 'ASC',
                    't.column2' => 'DESC',
                ],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'ASC',
                        'column2' => 'ASC',
                    ],
                ],
                [
                    't.column1' => 'ASC',
                    'column2' => 'ASC',
                ],
            ],
            [
                [
                    'order' => [
                        'column1' => 'ASC',
                        'column2' => 'foobar',
                    ],
                ],
                [
                    'column1' => 'ASC',
                    'column2' => 'ASC',
                ],
            ],
        ];
    }

    /**
     * Data provider method for 'testThatGetLimitReturnsExpectedValue' test.
     *
     * @return array
     */
    public function dataProviderTestThatGetLimitReturnsExpectedValue(): array
    {
        return [
            [
                ['limit' => 10],
                10,
            ],
            [
                ['limit' => 'ddd'],
                0,
            ],
            [
                ['limit' => 'E10'],
                0,
            ],
            [
                ['limit' => -10],
                10,
            ],
        ];
    }

    /**
     * Data provider method for 'testThatGetOffsetReturnsExpectedValue' test.
     *
     * @return array
     */
    public function dataProviderTestThatGetOffsetReturnsExpectedValue(): array
    {
        return [
            [
                ['offset' => 10],
                10,
            ],
            [
                ['offset' => 'ddd'],
                0,
            ],
            [
                ['offset' => 'E10'],
                0,
            ],
            [
                ['offset' => -10],
                10,
            ],
        ];
    }

    /**
     * Data provider method for 'testThatGetSearchTermsReturnsExpectedValue' test.
     *
     * @return array
     */
    public function dataProviderTestThatGetSearchTermsReturnsExpectedValue(): array
    {
        return [
            [
                [
                    'or' => [
                        '1',
                    ],
                ],
                true,
            ],
            [
                [
                    'or' => [
                        'bar',
                    ],
                ],
                'bar',
            ],
            [
                [
                    'or' => [
                        'bar',
                        'foo',
                    ],
                ],
                'bar foo',
            ],
            [
                [
                    'or' => [
                        'bar',
                        'f',
                        'oo',
                    ],
                ],
                'bar  f    oo ',
            ],
            [
                [
                    'and' => [
                        'foo',
                    ],
                ],
                '{"and": ["foo"]}'
            ],
            [
                [
                    'or' => [
                        'bar',
                    ],
                ],
                '{"or": ["bar"]}'
            ],
            [
                [
                    'and' => [
                        'foo',
                        'bar',
                    ],
                ],
                '{"and": ["foo", "bar"]}'
            ],
            [
                [
                    'or' => [
                        'bar',
                        'foo',
                    ],
                ],
                '{"or": ["bar", "foo"]}'
            ],
            [
                [
                    'or' => [
                        'bar',
                        'foo',
                    ],
                    'and' => [
                        'foo',
                        'bar',
                    ],
                ],
                '{"or": ["bar", "foo"], "and": ["foo", "bar"]}'
            ],
            [
                [
                    'or' => [
                        '{"or":',
                        '["bar",',
                        '"foo"],',
                    ],
                ],
                '{"or": ["bar", "foo"], ', // With invalid JSON input it should fallback to string handling
            ],
        ];
    }
}

<?php
namespace TandF\Helpers;

/**
 * Class UtilityTest
 * @package TandF\Helpers
 */
class UtilityTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function setUp()
    {

    }

    /**
     *
     */
    public function testGetIgo()
    {
        $items[0]['isbn'] = '9781466565463';
        $items[0]['price'] = '62.96';
        $items[0]['quantity'] = 3;

        $items[1]['isbn'] = '9781439871768';
        $items[1]['price'] = '161.96';
        $items[1]['quantity'] = 1;

        $results = Utility::getIgo($items);
        $this->assertInternalType('array', $results);
        $keys = ['rtaConvertCart', 'rtaOrderNum', 'rtaCart', 'rtaCartAmounts', 'rtaCartQuantities'];
        foreach ($keys as $k => $key) {
            $this->assertArrayHasKey($key, $results);
        }
    }

    public function testTokenTruncate()
    {
        $str = '1 2 3 4 5 6 7 8 9 11 14';
        $this->assertEquals('1 2 3 4 5 ...', Utility::tokenTruncate($str, 10));

        $str = '';
        $this->assertEquals('', Utility::tokenTruncate($str, 10));

        $str = '1 2 3';
        $this->assertEquals('1 2 3', Utility::tokenTruncate($str, 10));

        $str = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus facilisis condimentum semper. Vestibulum id velit.';
        $this->assertEquals('Lorem ipsum ...', Utility::tokenTruncate($str, 12));
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ...', Utility::tokenTruncate($str, 63));
    }

    public function testConvertStringToUtf8()
    {
        // test if utf-8 encoding is working
        $str = 'MATLAB®';
        $str = Utility::convertStringToUtf8($str);
        $this->assertEquals('"MATLAB\u00ae"', json_encode($str));
        
        // test if double utf-8 encoding is not happenning
        $str = 'MATLAB®';
        $str = Utility::convertStringToUtf8($str);
        $str = Utility::convertStringToUtf8($str);
        $this->assertEquals('"MATLAB\u00ae"', json_encode($str));
    }

    /* */
}
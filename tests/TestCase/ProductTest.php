<?php namespace TandF\Helpers;

use TandF\Ecommerce\Entity\Originator,
    TandF\Ecommerce\Entity\OriginatorInfo,
    TandF\Ecommerce\Entity\OriginatorRole;

/**
 * Class ProductTest
 * @package TandF\Helpers
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testGetOriginatorsLastName()
    {
        $myOriginator = new OriginatorInfo();

        $results = Product::getOriginatorsLastNames(['']);
        $this->assertInternalType('array', $results);
        $this->assertEmpty($results);

        $myOriginator->setName('John Smith');
        $results = Product::getOriginatorsLastNames($myOriginator);
        $this->assertInternalType('array', $results);
        $this->assertEquals('Smith', $results[0]);

        $originators = ['John Smith', 'Jane Doe', 'Benjamin Franklin'];
        $results = Product::getOriginatorsLastNames($originators);
        $this->assertInternalType('array', $results);
        $this->assertEquals('Smith', $results[0]);
        $this->assertEquals('Doe', $results[1]);
        $this->assertEquals('Franklin', $results[2]);

    }

    /**
     *
     */
    public function testProductUrl()
    {
        $title = 'DDoS Attacks: Evolution, Detection, Prevention, Reaction, and Tolerance by Cañas';
        $originators = ['Bhattacharyya', 'Kalita', 'Cañas', 'Catalão'];
        $isbn = 9781498729642;
        $type = 'book';

        $results = Product::productUrl($isbn, $title, $originators, $type);
        $expected = '/DDoS-Attacks-Evolution-Detection-Prevention-Reaction-and-Tolerance/Bhattacharyya-Kalita-Canas-Catalao/p/book/9781498729642';
        $this->assertEquals($expected, $results);

        $title = 'DDoS Attacks: Evolution, Detection, Prevention, Reaction, and Tolerance';
        $results = Product::productUrl($isbn, $title, $originators, $type);
        $expected = '/DDoS-Attacks-Evolution-Detection-Prevention-Reaction-and-Tolerance/Bhattacharyya-Kalita-Canas-Catalao/p/book/9781498729642';
        $this->assertEquals($expected, $results);

        $title = 'Evolution, Detection, Prevention, Reaction, Cañas, and Tolerance';
        $results = Product::productUrl($isbn, $title, $originators, $type);
        $expected = '/Evolution-Detection-Prevention-Reaction-Canas-and-Tolerance/Bhattacharyya-Kalita-Canas-Catalao/p/book/9781498729642';
        $this->assertEquals($expected, $results);

        $title = 'DDoS Attacks: Evolution, Detection, Prevention, Reaction, and Tolerance by Cañas';
        $type = 'journal';
        $results = Product::productUrl($isbn, $title, $originators, $type);
        $expected = '/DDoS-Attacks-Evolution-Detection-Prevention-Reaction-and-Tolerance/Bhattacharyya-Kalita-Canas-Catalao/p/journal/9781498729642';
        $this->assertEquals($results, $expected);

        $type = 'booksandstuff';
        $results = Product::productUrl($isbn, $title, $originators, $type);
        $expected = '/DDoS-Attacks-Evolution-Detection-Prevention-Reaction-and-Tolerance/Bhattacharyya-Kalita-Canas-Catalao/p/book/9781498729642';
        $this->assertEquals($results, $expected);

        $originators = null;
        $results = Product::productUrl($isbn, $title, $originators, $type);
        $expected = '/DDoS-Attacks-Evolution-Detection-Prevention-Reaction-and-Tolerance/author/p/book/9781498729642';
        $this->assertEquals($results, $expected);

    }
    /* */
}
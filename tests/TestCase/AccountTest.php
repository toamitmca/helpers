<?php namespace TandF\Helpers;

use TandF\Ecommerce\EcommerceAPI;
/**
 * Class AccountTest
 * @package TandF\Helpers
 */
class AccountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShopperService
     */
    private $shopperService;

    /**
     * @var InvoiceManagerService
     */
    private $invoiceService;

    /**
     * Instanciate the Ecommerce services/entities required for emulating model methods.
     */
    public function setUp()
    {
        $config = [
            'host' => 'http://dev01.blogic.crcpress.local/',
            'app_login' => 'www.crcpress.com',
            'app_pass' => 'overlord',
            'extra_config' => [
                'user_agent' => 'CRC PHP Soap client 2.0',
                'connection_timeout' => 6,
                'cache_wsdl' => \WSDL_CACHE_MEMORY,
                'trace' => true,
                'soap_version' => \SOAP_1_1,
                'encoding' => 'UTF-8'
            ],
        ];
        $ecommerceApi = new EcommerceAPI($config);
        $this->shopperService = $ecommerceApi->getService('Shopper');
        $this->invoiceService = $ecommerceApi->getService('InvoiceManager');
    }


    /**
     *
     */
    public function testCheckOrderCompleteExpiration()
    {
        $today = date('Y-m-d');
        $results = Account::checkOrderCompleteExpiration($today);
        $this->assertFalse($results);

        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $results = Account::checkOrderCompleteExpiration($yesterday);
        $this->assertTrue($results);
    }


    /**
     *
     */
    public function testCheckInvoiceOwnership()
    {
        $invoice = $this->invoiceService->findInvoiceById(294233);
        $shopper = $this->shopperService->findById(245644);
        $results = Account::checkInvoiceOwnership($invoice, $shopper);
        $this->assertTrue($results);

        $shopper = $this->shopperService->findByLoginName('william.sander@informausa.com');
        $results = Account::checkInvoiceOwnership($invoice, $shopper);
        $this->assertFalse($results);
    }

    /**
     *
     */
    public function testValidateCheckoutRedirection()
    {
        $types = ['chooseprofile', 'revieworder', 'account', 'review', 'greview'];
        foreach ($types as $t => $type) {
            $results = Account::validateCheckoutRedirection($type);
            if ($t < 3) {
                $this->assertEquals($type, $results);
            } else {
                $this->assertEquals('checkout/review', $results);
            }
        }

        $default = 'haha';
        $results = Account::validateCheckoutRedirection($default);
        $this->assertEquals('chooseprofile', $results);
    }

    /**
     *
     *
    public function testGetTotals()
    {
        $id = 294284;
        $oInvoice = $this->shopperModel->getInvoice($id);
        $shippingTax = $oInvoice->getShippingTerms()->getTax();
        $shippingPrice = $oInvoice->getShippingTerms()->getPrice();
        $invoiceTax = $oInvoice->getItemTax();
        $inv = array(
            'invID' => '',
            'invNumber' => '',
            'invDate' => '',
            'promocode' => '',
            'invPrice' => '',
            'invTax' => '',
            'invShipMethod' => '',
            'invType'   =>  '',
            'invShipMessage' => '',
            'invShipTermPrice' => number_format($oInvoice->getShippingTerms()->getPrice(), 2),
        );
        $invoiceData = $this->shopperModel->getInvoiceData($oInvoice, $invoiceTax, $shippingPrice, $shippingTax, false);
        $invoice = array_merge($inv, $invoiceData['invoiceItems']);
        $invoices[] = $invoice;
        $results = Account::getTotals($invoices);

        $this->assertInternalType('array', $results);
        $keys = ['totalPrice', 'savings', 'subtotal', 'tax', 'invShipTermPrice'];
        foreach ($keys as $k => $key) {
            $this->assertArrayHasKey($key, $results);
        }
    }

    /**
     *
     */
    public function testIsAuthenticated()
    {
        $shopper = array();
        $results = Account::isAuthenticated($shopper);
        $this->assertFalse($results);

        $shopper['id'] = 245644;
        $results = Account::isAuthenticated($shopper);
        $this->assertTrue($results);
    }
    /* */
}
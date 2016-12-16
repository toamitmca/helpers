<?php

namespace TandF\Helpers;

/**
 * Utility class for assisting with General
 *
 * @package CrcFramework
 * @subpackage Helpers
 * @copyright Taylor and Francis 2014
 * @author Stephen Hearn
 */

class Utility {


    /**
     *  Builds the igo variables for recommendations
     *
     * @param array $items
     * @return array $iGo
     */
    public static function getIgo($items) {

        $iGo = array();
        $iGo['rtaConvertCart'] = '';
        $iGo['rtaOrderNum'] = '';

        $rtaCart = array();
        $rtaCartAmounts = array();
        $rtaCartQty = array();

        foreach ($items as $data) {

            $rtaCart[]        = $data['isbn'];
            $rtaCartAmounts[] = $data['price'];
            $rtaCartQty[]     = $data['quantity'];
        }

        $iGo['rtaCart']           = implode('|', $rtaCart);
        $iGo['rtaCartAmounts']    = implode('|', $rtaCartAmounts);
        $iGo['rtaCartQuantities'] = implode('|', $rtaCartQty);

        return $iGo;
    }

    /**
     * Compare First Names: to sort originators by First Name.
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    public static function cmpFirstName($a, $b)
    {
        return strcmp($a["firstName"], $b["firstName"]);
    }

    /**
     * Compare Last Names: to sort originators by Last Name.
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    public static function cmpLastName($a, $b)
    {
        return strcmp($a["lastName"], $b["lastName"]);
    }

    /**
     * Makes sure the passed value is a valid isbn string
     *
     * @param $string
     * @return string/boolean
     */
    public static function isIsbn($string)
    {
        $string = str_replace('-', '', $string);
        $pattern = "/(\d{1,3}([-])\d{1,5}\2\d{1,7}\2\d{1,6}\2(\d|X))|(\d{1,5}([-])\d{1,7}\5\d{1,6}\5(\d|X))|(\d{13})|(\d{10})/";
        return preg_match($pattern, $string, $matches) ? $matches[0] : false;
    }

    /**
     * This method handles any curl calls
     *
     * @param string $url
     * @return mixed $data
     */
    public static function doCurl($url) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * Ensures that $unsafeType is an array
     * Useful to deal with functions that have inconsistent return types
     *
     * @param $unsafeType
     * @return array
     */
    public static function ensureArray($unsafeType)
    {
        switch (true) {
            case is_array($unsafeType):
                $return = $unsafeType;
                break;
            case empty($unsafeType):
                $return = array();
                break;
            default:
                $return = array($unsafeType);
                break;
        }
        return $return;
    }

    /**
     * @param $cardtype
     * @return string
     */
    public static function setIssuer($cardtype)
    {
        switch ($cardtype) {
            case '001':
                return "Visa";
            case '002':
                return "MasterCard";
            case '003':
                return "AmericanExpress";
            default:
                return "Visa";
        }
    }

    /**
     * For sorting category hits into alphabetical order
     *
     * @param Category $a
     * @param Category $b
     * @return boolean
     */
    public static function cmpCategories($a, $b) {

        if (($a instanceof Category) && ($b instanceof Category)) {

            return \strncmp($a->getTitle(), $b->getTitle(), 20);
        }

        return 0;
    }

    /**
     * Checks that the passed object is a Category and also is not the root CRC category.
     *
     * @param Category $mCat
     * @return boolean
     */
    public static function checkBreadcrumbCategory($mCat) {

        if (($mCat instanceof Category)) {

            if ($mCat->getCode() != 'CRC') {

                return true;
            }
        }

        return false;
    }

    /**
     * Function sorts countries into alphabetical order.
     *
     * @param Country $a
     * @param Country $b
     * @return int
     */
    public static function cmpCountries($a, $b) {

        if (($a instanceof Country) && ($b instanceof Country)) {

            return \strncmp($a->getName(), $b->getName(), 3);
        }

        return 0;
    }

    /**
     * For sorting category hits into alphabetical order
     *
     * @param Category $a
     * @param Category $b
     * @return boolean
     */
    public static function cmpSeries($a, $b)
    {

        if (($a instanceof Series) && ($b instanceof Series)) {

            return \strncmp($a->getDescription(), $b->getDescription(), 100);
        }

        return 0;
    }

    /**
     * Sorts and formats series results
     *
     * @param PaginatedSeriesResults $series
     * @return array $return
     */
    public static function sortSeries($series)
    {
        $return = array();

        if ($series instanceof PaginatedSeriesResults) {
            $seriesObjects = $series->getResults();
            usort($seriesObjects, "Utility::cmpSeries");
            foreach ($seriesObjects as $series) {
                if ($series instanceof Series) {
                    $return[$series->getCode()] = $series->getDescription();
                }
            }
        }

        return $return;
    }

    /**
     * Returns the value or null
     *
     * @param $v
     * @return null
     */
    public static function valOrNull($v)
    {
        return $v !== false ? $v : null;
    }

    /**
     * extracts SA vars from $_POST and returns them as an array.
     * Missing params are present in the array but value is null
     *
     * @param array $post
     * @return array
     */
    public static function getSaVars($post)
    {
        if ($post['decision'] === 'ACCEPT') {
            list($expMo, $expYr) = preg_split('/-/', $post['req_card_expiry_date']);
            return array(
                'reasoncode' => Utility::valOrNull($post['reason_code']),
                'transactionid' => Utility::valOrNull($post['transaction_id']),
                'shopperid'     => Utility::valOrNull($post['shopperId']),
                'profileid'     => Utility::valOrNull($post['profileId']),
                'cartid'        => Utility::valOrNull($post['cartId']),
                'cardtype'      => Utility::valOrNull($post['req_card_type']),
                'accountnumber' => Utility::valOrNull($post['req_card_number']),
                'cardmonth'     => $expMo,
                'cardyear'      => $expYr,
                'name'          => Utility::valOrNull($post['req_bill_to_forename']) . ' ' . Utility::valOrNull($post['req_bill_to_surname'])
            );
        }
        return false;
    }

    /**
     * @param $shipmethods
     *
     * Creates an array with shipping method codes
     *
     * @return array
     */
    public function getShipMethodCodes($shipmethods)
    {

        $result = array();

        foreach ($shipmethods as $data) {

            $result[] = $data->getCode();
        }

        return $result;
    }

    /**
     *
     * Used for sorting shipping terms array
     *
     * @param $a - shipping option A
     * @param $b - shipping option B
     *
     * @return int
     */
    public static function sortShippingOptions($a, $b)
    {

        if ($a->getAdjustedPrice() == $b->getAdjustedPrice()) {

            return 0;
        }

            return ($a->getAdjustedPrice() < $b->getAdjustedPrice()) ? -1 : 1;
    }

    /**
     * Truncate a string to a desired width and adding ellipse if needed
     *
     * @param $string string to be trimmed
     * @param $width desired length
     * @return string
     */
    public static function tokenTruncate($string, $width)
    {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $width) { break; }
        }

        $rtn = implode(array_slice($parts, 0, $last_part));
        if ($rtn !== $string) {
            $rtn = $rtn . '...';
        }

        return $rtn;
    }

    /**
     * Builds vanity series URL
     *
     * @param $series
     * @param $title
     * @return string
     */
    public static function seriesUrl($series, $title)
    {
        $title = trim($title);

        $titleCut = substr($title, 0, @strpos($title, ' ', 70));

        if($titleCut) {
            $titleUrl = str_replace(' ', '-',preg_replace("/[^A-Za-z0-9 -]/", '', $titleCut));
        } else {
            $titleUrl = str_replace(' ', '-',preg_replace("/[^A-Za-z0-9 -]/", '', $title));
        }

        return "/$titleUrl/book-series/$series";
    }

    /**
     * Generate the email body for bulk orders upsell
     *
     * @param $cart array
     * @param $currencySymbol string
     * @return string
     */
    public static function buildSpecialSalesMailBody($cart, $currencySymbol) {
        $body = 'I am interested in making a bulk purchase on CRCPress.com and would like to get the best deal on the following order:%0D%0D';
        foreach ($cart['items'] as $item) {
            $body .= $item['isbn'] . '%20(' . $item['quantity'] . ' copy - ' . $currencySymbol . $item['price'] . '/ea)%0D';
        }
        $body .= '%0DThe price in my shopping cart is currently ' . $currencySymbol . $cart['totaltotal'] . ' for all ' . $cart['itemcount'] . ' items.';
        $body .= '%0DPlease let me know if I qualify for a special discount.%0D%0DThank you';
        return $body;
    }
    
    /**
     * encode non-utf8 string to utf-8 string
     *
     * @param $string string
     * @return string
     */
    public static function convertStringToUtf8($string)
    {
        //check if string is not already utf-8 encoded
        if (mb_detect_encoding($string, 'UTF-8', true) === false) {
            $string = utf8_encode($string);
        }
        return $string;
    }
}
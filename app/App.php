<?php
namespace App;

class App{

    public  $eu_issued_rate = 0.01;
    public  $eu_non_issued_rate = 0.02;

    public $bin;
    public $amount;
    public $currency;


    public $bin_status;

    public $inputStr;
    
    public $bin_api = "https://lookup.binlist.net/";
    public $rate_api = "https://api.exchangeratesapi.io/latest";

    
    public $rate;

    public $eu_issued_country = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 
    'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT','LU', 'LV', 'MT', 'NL', 'PO', 'PT',
    'RO', 'SE', 'SI', 'SK');
    
    public function __construct(){
        
    }
    /**
     * Get & Set eu_issued_rate property
     */
    public function setEuIssuedRate($euIssuedRate)
    {
        $this->eu_issued_rate = $euIssuedRate;
    }
    public function getEuIssuedRate(){
        return $this->eu_issued_rate;
    }

    /**
     * Get & Set eu_non_issued_rate property
     */
    public function setEuNonIssuedRate($euNonIssuedRate)
    {
        $this->eu_non_issued_rate = $euNonIssuedRate;
    }
    public function getEuNonIssuedRate(){
        return $this->eu_non_issued_rate;
    }

    /**
     * Get & Set bin property
     */
    public function setBin($bin)
    {
        $this->bin = $bin;
    }
    public function getBin(){
        return $this->bin;
    }

    /**
     * Get & Set amount property
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get & Set currency property
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get & Set bin_api property
     */
    public function setBinApiUrl($binApi)
    {
        $this->bin_api = $binApi;
    }
    public function getBinApiUrl()
    {
        return $this->bin_api;
    }

    /**
     * Get & Set rate_api property
     */
    public function setRateApiUrl($rateApi)
    {
        $this->rate_api = $rateApi;
    }
    public function getRateApiUrl()
    {
        return $this->rate_api;
    }

    /**
     * Get & Set eu_issued_country property
     */
    public function setEuIssuedCountryList($listCountry = array())
    {
        $this->eu_issued_country = $listCountry;
    }
    public function GetEuIssuedCountryList()
    {
        return $this->eu_issued_country;
    }


    /**
     * Function setBinStatus
     * This function will check the input bin is issued or not. Bin is checked from
     * api url
     * 
     * return boolean status
     */
    public function setBinStatus($binResults = array()){
        if(empty($binResults)){
            // Generate binResult data from api
            $binApi = $this->bin_api.$this->bin;
            $binResults = file_get_contents($binApi);

            if (!$binResults)
                die('error!');
            $binResults = json_decode($binResults, true);
            $this->bin_status = (in_array($binResults['country']['alpha2'], $this->eu_issued_country)) ? true : false;
        }else{
            $this->bin_status = (in_array($binResults[$this->bin]['country']['alpha2'], $this->eu_issued_country)) ? true : false;
        } 
    }

    public function getBinStatus(){
        return $this->bin_status;
    }

    
    /**
     * Function:: setRate
     * This function will set the rate based on currency
     */
    public function setRate(){
        $rateRawData = json_decode(file_get_contents($this->rate_api), true);
        $this->rate = @$rateRawData['rates'][$this->currency];
        
    }

    public function getRate(){
        return $this->rate;
    }

    /**
     * Function :: commission
     * This function will calculate the final commission
     */
    public function getCommission(){
        $currency = $this->currency;
        $rate = $this->rate;
        $isEu = $this->bin_status;

        if($currency == 'EUR' || $rate == 0){
            $amountFixed = $this->amount;
        }elseif($currency != 'EUR' || $rate > 0){
            $amountFixed = $this->amount / $rate;
        }
        
        $commission = $amountFixed * (($isEu) ? $this->eu_issued_rate : $this->eu_non_issued_rate);

        $commission = $this->makeCeilOfFractionPart($commission);

        return $commission;
    }

    /** 
     * Function : makeCeilOfFractionPart
     * This function will make ceil of fractional part
     */
    public function makeCeilOfFractionPart($number, $precision = 2, $separator = ".")
    {
        $explodeNumber = explode($separator, $number);
        
        if(isset($explodeNumber[1]) && $explodeNumber[1] > 0)
        {
            $explodeNumber[1] = substr_replace($explodeNumber[1], $separator ,$precision,1);

            if($explodeNumber[1] >= 0)
            {
                $explodeNumber[1] = ceil($explodeNumber[1]);
            }
            else
            {
                $explodeNumber[1] = floor($explodeNumber[1]);
            }

            return implode($separator, $explodeNumber);
        }else{
            return $number;
        }
        
        
        

        
        
    }

    /**
     * Function:: parseStr
     * This function will  parse each row of string to get the desired value
     * 
     */
    public function parseStr(){
        $p = explode(",", $this->inputStr);

        $p2 = explode(':', $p[0]);
        $this->bin = trim($p2[1], '"');
        
        $p2 = explode(':', $p[1]);
        $this->amount = trim($p2[1], '"');
        
        $p2 = explode(':', $p[2]);
        $curr = ltrim(trim($p2[1]), '"');
        $this->currency = rtrim($curr, '"}');

    }

} // End of App Class


/*foreach (explode("\n", file_get_contents($argv[1])) as $row){
    $comm = new App();

    $comm->inputStr = $row;
    
    $comm->parseStr();

    $comm->setBinStatus();
    $comm->setRate();

    echo $comm->getCommission()."\n";
}*/


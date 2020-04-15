<?php

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{   
    protected $app;

    public function setUp():void
    {
        $this->app = new App\App;
    }

    /** @test */
    public function weCanGetTheBinNumber()
    {
        $this->app->setBin("123456");
        $this->assertEquals($this->app->getBin(), "123456");
    }

    /** @test */
    public function weCanGetTheAmount()
    {
        $this->app->setAmount(123);
        $this->assertEquals($this->app->getAmount(), 123);
    }

    /** @test */
    public function weCanGetTheCurrency()
    {
        $this->app->setCurrency('USD');
        $this->assertEquals($this->app->getCurrency(), 'USD');
    }

    /** @test 
     * By this we can test the bin number is issue or not
    */
    public function weCanGetTheStatusTheBinIsIssuedOrNot()
    {
        
        $this->app->setBinApiUrl("https://lookup.binlist.net/"); // For online test
        
        $this->app->setEuIssuedCountryList(
            array(
                'US',   // bin 41417360
                'DK',   // bin 45717360
                'JP'    // bin 45417360
            )
        );

        // GB - that return false
        //$this->app->setBin("4745030");

        // US - that return true
        $this->app->setBin("41417360"); 

        // For online test no need to pass data
        $this->app->setBinStatus();

        // For offline test we can pass data same as like api
        /*$this->app->setBinStatus(
            array(
                '41417360' => array(
                    'country' => array(
                    'alpha2' => 'US'
                    )
                ),
                '45717360' => array(
                    'country' => array(
                    'alpha2' => 'DK'
                    )
                ),
                '4745030' => array(
                    'country' => array(
                    'alpha2' => 'GB'
                    )
                )
                
            )
        );*/
        
        $this->assertTrue($this->app->getBinStatus());
    }


    /** @test 
     * This function get rate from apibased on currency
    */
    public function weCanGetRate()
    {
        $this->app->setCurrency("USD");
        $this->app->setRate();
        $this->assertEquals($this->app->getRate(), 1.0963);
    }

    /** @test 
     * This function will test commission based on bin, amount & currency
    */
    public function weCangetCommission()
    {
        $this->app->setBin("41417360");
        $this->app->setAmount(130.00);
        $this->app->setCurrency("USD");

        $this->app->setBinStatus();
        $this->app->setRate();

        $this->assertEquals($this->app->getCommission(), 2.38);

    }

    /** @test 
     * This function will test for ceil value of fractional part
    */
    public function weCanGetCeilValueOfFractionalPart(){
        $this->assertEquals($this->app->makeCeilOfFractionPart(2.3716136094135), 2.38);
    }
}
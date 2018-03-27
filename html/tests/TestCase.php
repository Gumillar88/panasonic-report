<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        foreach(\DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            
            $name = $table_array[key($table_array)];
            
            if ($name == 'migrations') {
                continue;
            }
            
            DB::table($name)->truncate();
        }
    }
    
    public function tearDown()
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });
        
        parent::tearDown();
    }
    
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        
        if (file_exists(dirname(__DIR__) . '/.env.test')) {
            (new \Dotenv\Dotenv(dirname(__DIR__), '.env.test'))->load();
        }

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
    
    /**
     * Simple http result container
     *
     * @access protected
     */
    protected $_httpResult;
    
    /**
     * Simple wrapper for request
     *
     * @access public
     * @param String $method
     * @param String $path
     * @param Array $data
     * @return TestCase instance
     */
    protected function _request($method, $path, $data = [])
    {
        $response   = $this->call($method, $path, $data);
        
        $this->_httpResult = json_decode($response->getContent(), true);
        
        return $this;
    }
    
    /**
     * Simple wrapper for checking http result
     *
     * @access protected
     * @param Array $data
     * @return Void
     */
    protected function _result($data)
    {
        $this->assertEquals($this->_httpResult, $data);
    }
    
    /**
     * Helper class to pick random item on array
     *
     * @access protected
     * @param Array $data
     * @return Mixed
     */
    protected function _pickRandomItem($data)
    {
        return $data[rand(0, (count($data) - 1))];
    }
    
    
    /**
     * Inject mocked class to testing instance
     *
     * @access protected
     * @param Array $data
     * @return Void
     */
    public function _inject($data)
    {
        foreach ($data as $meta => $mock)
        {
            $this->app->instance($meta, $mock);
        }
    }
    
    /**
     * In text page assertion to check if page contain specific text
     *
     * @access protected
     * @param String $string
     * @return Void
     */
    protected function assertPageContain($string)
    {
        // Set error message
        $message = 'Page doesn\'t contain "'.$string.'"';
        
        $this->assertTrue(strpos($this->response->getContent(), $string) !== false, $message);
    }
    
    /**
     * In text page assertion to check if page not contain specific text
     *
     * @access protected
     * @param String $string
     * @return Void
     */
    protected function assertPageNotContain($string)
    {
        // Set error message
        $message = 'Page contain "'.$string.'"';
        
        $this->assertFalse(strpos($this->response->getContent(), $string) !== false, $message);
    }
}

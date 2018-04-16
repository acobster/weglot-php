<?php

use Weglot\Client\Client;

class ClientTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Weglot\Client\Client
     */
    protected $client;

    /**
     * Init client
     */
    protected function _before()
    {
        $this->client = new Client(getenv('WG_API_KEY'));
    }

    // tests
    public function testOptions()
    {
        $options = $this->client->getOptions();

        $this->assertEquals('https://api.weglot.com', $options['host']);
        $this->assertEquals('Weglot/' .Client::VERSION, $options['user-agent']);
    }

    public function testConnector()
    {
        $connector = $this->client->getConnector();

        $this->assertTrue($connector instanceof GuzzleHttp\Client);
        $this->assertEquals('api.weglot.com', $connector->getConfig('base_uri')->getHost());
        $this->assertEquals('application/json', $connector->getConfig('headers')['Content-Type']);
        $this->assertEquals('Weglot/' .Client::VERSION, $connector->getConfig('headers')['User-Agent']);
    }

    public function testProfile()
    {
        $wgApiKeys = [
            'wg_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa' => 1,
            'wg_bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' => 2
        ];
        foreach ($wgApiKeys as $wgApiKey => $version) {
            $client = new Client($wgApiKey);
            $profile = $client->getProfile();

            $this->assertEquals($version, $profile->getApiVersion());
            $this->assertEquals($version === 2, $profile->getIgnoredNodes());
        }
    }

    public function testMakeRequest()
    {
        $response = $this->client->makeRequest('GET', '/status', []);
        $this->assertEquals([], $response);
    }

    public function testMakeRequestAsResponse()
    {
        $response = $this->client->makeRequest('GET', '/status', [], false);
        $this->assertTrue($response->getStatusCode() === 200);
    }

    public function testMakeRequestThrowGuzzleException()
    {
        $this->expectException(\Weglot\Client\Api\Exception\ApiError::class);

        $this->client->setOptions([
            'host'  => 'https://foo.bar.baz',
        ]);
        $response = $this->client->makeRequest('GET', '/status', []);
    }
}

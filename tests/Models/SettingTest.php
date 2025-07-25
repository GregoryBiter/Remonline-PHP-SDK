<?php

use PHPUnit\Framework\TestCase;
use Gbit\Remonline\Models\Setting;
use Gbit\Remonline\RemonlineClient;

class SettingTest extends TestCase
{
    private $apiMock;
    private $setting;

    protected function setUp(): void
    {
        $this->apiMock = $this->createMock(RemonlineClient::class);
        $this->setting = new Setting($this->apiMock);
    }

    public function testGetCompanySetting()
    {
        $this->apiMock->method('getData')
            ->with('settings/company', [])
            ->willReturn(['company' => 'Test']);

        $result = $this->setting->getCompanySetting();
        $this->assertEquals(['company' => 'Test'], $result);
    }

    public function testGetLocations()
    {
        $this->apiMock->method('getData')
            ->with('branches/', [])
            ->willReturn(['locations' => []]);

        $this->setting = $this->getMockBuilder(Setting::class)
            ->setConstructorArgs([$this->apiMock])
            ->onlyMethods(['response'])
            ->getMock();
        $this->setting->expects($this->once())
            ->method('response')
            ->with(['locations' => []])
            ->willReturn(['locations' => []]);

        $result = $this->setting->getLocations();
        $this->assertEquals(['locations' => []], $result);
    }

    public function testGetAdCampaigns()
    {
        $this->apiMock->method('getData')
            ->with('marketing/campaigns/', [])
            ->willReturn(['campaigns' => []]);

        $this->setting = $this->getMockBuilder(Setting::class)
            ->setConstructorArgs([$this->apiMock])
            ->onlyMethods(['response'])
            ->getMock();
        $this->setting->expects($this->once())
            ->method('response')
            ->with(['campaigns' => []])
            ->willReturn(['campaigns' => []]);

        $result = $this->setting->getAdCampaigns();
        $this->assertEquals(['campaigns' => []], $result);
    }

    public function testGetPrices()
    {
        $this->apiMock->method('getData')
            ->with('margins/', [])
            ->willReturn(['prices' => []]);

        $result = $this->setting->getPrices();
        $this->assertEquals(['prices' => []], $result);
    }

    public function testGetEmployees()
    {
        $this->apiMock->method('getData')
            ->with('employees/', [])
            ->willReturn(['employees' => []]);

        $result = $this->setting->getEmployees();
        $this->assertEquals(['employees' => []], $result);
    }

    public function testGetOrderTypes()
    {
        $this->apiMock->method('request')
            ->with('orders/types', [], 'GET')
            ->willReturn(['types' => []]);

        $result = $this->setting->getOrderTypes();
        $this->assertEquals(['types' => []], $result);
    }

    public function testGetOrderCustomFields()
    {
        $this->apiMock->method('request')
            ->with('orders/custom-fields', [], 'GET')
            ->willReturn(['fields' => []]);

        $result = $this->setting->getOrderCustomFields();
        $this->assertEquals(['fields' => []], $result);
    }

    public function testGetBookList()
    {
        $this->apiMock->method('getData')
            ->with('book/list/', [])
            ->willReturn(['books' => []]);

        $result = $this->setting->getBookList();
        $this->assertEquals(['books' => []], $result);
    }
}

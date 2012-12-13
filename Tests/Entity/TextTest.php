<?php
namespace Networking\InitCmsBundle\Tests\Entity;

use \Networking\InitCmsBundle\Entity\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
	public function testOnPrePersist()
	{
		$text = new Text();
		$this->assertEquals(null, $text->getUpdatedAt());
		$text->onPrePersist();
		$this->assertEquals(new \DateTime('now'), $text->getUpdatedAt());
		$this->assertEquals(new \DateTime('now'), $text->getCreatedAt());
	}

	public function testOnPreUpdate()
	{
		$text = new Text();
		$this->assertEquals(null, $text->getUpdatedAt());
		$text->onPreUpdate();
		$this->assertEquals(new \DateTime('now'), $text->getUpdatedAt());
		$this->assertEquals(null, $text->getCreatedAt());
	}

	public function testGetFieldDefinition()
	{
		$text = new Text();
		$result = $text->getFieldDefinition();
		$this->assertArrayHasKey('name', $result[0]);
		$this->assertArrayHasKey('type', $result[0]);
		$this->assertArrayHasKey('options', $result[0]);
	}

	public function testGetTemplateOptions()
	{
		$text = new Text();
		$randomText = '<strong>Some random text.</strong>';
		$text->setText($randomText);
		$result = $text->getTemplateOptions();
		$this->assertArrayHasKey('text', $result);
		$this->assertContains($randomText, $result);
	}

	public function testGetAdminContent()
	{
		$text = new Text();
		$randomText = '<strong>Some random text.</strong>';
		$text->setText($randomText);
		$result = $text->getAdminContent();
		$this->assertArrayHasKey('content', $result);
		$this->assertArrayHasKey('template', $result);
	}

}
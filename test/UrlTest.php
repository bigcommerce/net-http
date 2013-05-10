<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Net_UrlTest extends PHPUnit_Framework_TestCase
{

	function testEmptyBasePath()
	{
		$path = new Net_Url("/");
		$this->assertTrue($path->isEmpty());
		$this->assertEquals("/", $path->path());
		$this->assertEquals(0, count($path->segments()));
		$this->assertEquals("", $path->segment(0));
		$this->assertEquals("", $path->segment(1));
		$this->assertEquals("", $path->identity());
		$this->assertEquals("", $path->resource());
	}
	
	function testSingleSegmentPath()
	{
		$path = new Net_Url("/entry");
		$this->assertEquals(1, count($path->segments()));
		$this->assertEquals("/entry", $path->path());
		$this->assertEquals("entry", $path->identity());
		$this->assertEquals("entry", $path->resource());
	}
	
	function testMultiSegmentPath()
	{
		$path = new Net_Url("/content/entry/title");
		$parts = $path->segments();
		$this->assertEquals(3, count($parts));
		$this->assertEquals("title", $parts[2]);
		$this->assertEquals("entry", $parts[1]);
		$this->assertEquals("content", $parts[0]);
	}
	
	function testTrailingSlashIsDroppedFromSegmentPath()
	{
		$path = new Net_Url("/content/");
		$this->assertEquals(1, count($path->segments()));
		$this->assertEquals("/content/", $path->path());
		$this->assertEquals("content", $path->identity());
		$this->assertEquals("content", $path->resource());
		$path = new Net_Url("/content/entry/subject/id/");
		$this->assertEquals(array("content", "entry", "subject", "id"), $path->segments());
		$this->assertEquals(array("content", "entry", "subject", "id"), $path->segmentsFrom(0));
		$this->assertEquals(array("entry", "subject", "id"), $path->segmentsFrom(1));
		$this->assertEquals(array("subject", "id"), $path->segmentsFrom(2));
		$this->assertEquals(array("id"), $path->segmentsFrom(3));		
	}
	
	function testSegmentPathMethod()
	{
		$path = new Net_Url("/content/entry/subject");
		$this->assertEquals("content", $path->segment(0));
		$this->assertEquals("entry", $path->segment(1));
		$this->assertEquals("subject", $path->segment(2));
	}
	
	function testMultiSegmentPathMethods()
	{
		$path = new Net_Url("/content/entry/subject/id");
		$this->assertEquals(array("content", "entry", "subject", "id"), $path->segments());
		$this->assertEquals(array("content", "entry", "subject", "id"), $path->segmentsFrom(0));
		$this->assertEquals(array("entry", "subject", "id"), $path->segmentsFrom(1));
		$this->assertEquals(array("subject", "id"), $path->segmentsFrom(2));
		$this->assertEquals(array("id"), $path->segmentsFrom(3));
	}
	
	function testFragmentIdentifier()
	{
		$path = new Net_Url("/content/entry/title#heading");
		$this->assertEquals("title#heading", $path->resource());
		$this->assertEquals("title", $path->identity());
		$this->assertEquals("heading", $path->fragment());
	}
	
	function testPlusEncodedPath()
	{
		$path = new Net_Url("/entries/an+encoded+title");
		$this->assertEquals("an encoded title", $path->identity());
	}
	
	function testAutoEncodedPath()
	{
		$path = new Net_Url("/entries/an%20encoded%20title");
		$this->assertEquals("an encoded title", $path->identity());
	}	
	
	function testQueryString()
	{
		$path = new Net_Url("/?entry=title&id=123");
		$this->assertTrue($path->isEmpty());
		$this->assertEquals(2, count($path->parameters()));
		$this->assertEquals("title", $path->parameter("entry"));
		$this->assertEquals("123", $path->parameter("id"));
	}
	
	function testSingleSegmentPathWithQueryString()
	{
		$path = new Net_Url("/search?q=a+search+phrase");
		$this->assertEquals("search", $path->resource());
		$this->assertEquals("q=a+search+phrase", $path->query());
		$this->assertEquals("a search phrase", $path->parameter("q"));
	}
	
	function testMultiSegmentPathWithQueryString()
	{
		$path = new Net_Url("/entries/2005?page=3&tag=design");
		$this->assertEquals(2, count($path->segments()));
		$this->assertEquals("2005", $path->resource());
		$this->assertEquals("2005", $path->identity());
		$this->assertEquals("page=3&tag=design", $path->query());
		$this->assertEquals(2, count($path->parameters()));
		$this->assertEquals("3", $path->parameter("page"));
		$this->assertEquals("design", $path->parameter("tag"));
	}
	
	function testQueryParametersAsArray()
	{
		$path = new Net_Url("/base/object?q[0]=hello&q[1]=world&m[0]=foo&m[1]=bar");
		$this->assertEquals(2, count($path->parameters()));
		$q = $path->parameter('q');
		$m = $path->parameter('m');
		$this->assertInternalType('array', $q);
		$this->assertEquals("hello", $q[0]);
		$this->assertEquals("world", $q[1]);
		$this->assertEquals("foo", $m[0]);
		$this->assertEquals("bar", $m[1]);
	}

	function testMultiSegmentPathWithExtension()
	{
		$path = new Net_Url("/books/title.txt");
		$this->assertEquals("txt", $path->extension());
		$this->assertEquals("title.txt", $path->resource());
		$this->assertEquals("title", $path->identity());
	}
	
	function testMultiSegmentPathWithAspect()
	{
		$path = new Net_Url("/base/object;aspect");
		$this->assertEquals("aspect", $path->aspect());
		$this->assertEquals("object", $path->resource());
		$this->assertEquals("object", $path->identity());
	}
	
	function testMultiSegmentPathWithAspectAndExtension()
	{
		$path = new Net_Url("/base/identity.xml;edit");
		$this->assertEquals("edit", $path->aspect());
		$this->assertEquals("identity.xml", $path->resource());
		$this->assertEquals("xml", $path->extension());
		$this->assertEquals("identity", $path->identity());
	}
	
	function testCanRemoveBadCharacters()
	{
		$path = new Net_Url('/base/object.php?key=value<?php echo $_REQUEST; ?>');
		$this->assertEquals("object.php", $path->resource());
		$this->assertEquals("value", $path->parameter("key"));
	}

}
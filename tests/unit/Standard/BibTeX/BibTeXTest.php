<?php
namespace Geissler\Converter\Standard\BibTeX;

use ErrorException;
use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-05 at 12:03:05.
 */
class BibTeXTest extends TestCase
{
    /**
     * @var BibTeX
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = new BibTeX;
    }

    /**
     * @covers Geissler\Converter\Standard\BibTeX\BibTeX::__construct
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::__construct
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::setCreator
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::setParser
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::parse
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::create
     * @dataProvider dataProviderRun
     */
    public function testRun($bibTeX, $result)
    {
        $this->assertTrue($this->object->parse($bibTeX));
        $this->assertInstanceOf('\Geissler\Converter\Model\Entries', $this->object->retrieve());
        $this->assertEquals($result, $this->object->create($this->object->retrieve()));
    }

    public function dataProviderRun()
    {
        return array(
            array('@conference{conference,
  author       = {Peter Draper},
  title        = {The title of the work},
  booktitle    = {The title of the book},
  year         = 1993,
  editor       = {Jone Doe},
  volume       = 4,
  series       = 5,
  pages        = 213,
  address      = {The address of the publisher},
  month        = 7,
  organization = {The organization},
  publisher    = {The publisher},
  note         = {An optional note}
}
',
            '@conference{conference,
author = {Draper, Peter},
editor = {Doe, Jone},
year = {1993},
month = {7},
pages = {213},
title = {The title of the work},
volume = {4},
note = {An optional note},
publisher = {The publisher},
series = {5},
address = {The address of the publisher},
organization = {The organization},
booktitle = {The title of the book}
}'
            )
        );
    }

    /**
     * @covers Geissler\Converter\Standard\BibTeX\BibTeX::__construct
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::__construct
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::setCreator
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::setParser
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::parse
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::create
     * @covers Geissler\Converter\Standard\Basic\StandardAbstract::retrieve
     */
    public function testDoNotRun()
    {
        $this->assertFalse($this->object->parse());
        $this->assertEquals('', $this->object->create(new \Geissler\Converter\Model\Entries()));
        $this->expectException(ErrorException::class);
        $this->object->retrieve();
    }
}

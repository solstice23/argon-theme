<?php

namespace DiDom\Tests;

use DiDom\Document;
use DiDom\DocumentFragment;
use DOMElement;
use InvalidArgumentException;

class DocumentFragmentTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorWithInvalidNodeType()
    {
        new DocumentFragment(new DOMElement('span'));
    }

    public function testAppendXml()
    {
        $document = new Document();

        $documentFragment = $document->createDocumentFragment();

        $documentFragment->appendXml('<foo>bar</foo>');

        $this->assertEquals('<foo>bar</foo>', $documentFragment->innerXml());
    }
}

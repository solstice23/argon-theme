<?php

namespace DiDom\Tests;

use DiDom\Document;
use DiDom\Element;
use DiDom\Query;
use DOMComment;
use DOMDocument;
use DOMElement;
use DOMText;
use LogicException;
use InvalidArgumentException;
use RuntimeException;

class ElementTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorWithNullTagName()
    {
        new Element(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorWithInvalidTagNameType()
    {
        new Element([]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorWithInvalidObject()
    {
        new Element(new \StdClass());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorWithInvalidValue()
    {
        new Element('span', []);
    }

    public function testConstructorWithInvalidAttributes()
    {
        if (PHP_VERSION_ID >= 70000) {
            $this->setExpectedException('TypeError');
        } else {
            $this->setExpectedException('PHPUnit_Framework_Error');
        }

        new Element('span', 'Foo', null);
    }

    public function testConstructor()
    {
        $element = new Element('input', null, ['name' => 'username', 'value' => 'John']);

        $this->assertEquals('input', $element->getNode()->tagName);
        $this->assertEquals('username', $element->getNode()->getAttribute('name'));
        $this->assertEquals('John', $element->getNode()->getAttribute('value'));

        // create from DOMElement
        $node = new DOMElement('span', 'Foo');
        $element = new Element($node);

        $this->assertEquals($node, $element->getNode());

        // create from DOMText
        $node = new DOMText('Foo');
        $element = new Element($node);

        $this->assertEquals($node, $element->getNode());

        // create from DOMComment
        $node = new DOMComment('Foo');
        $element = new Element($node);

        $this->assertEquals($node, $element->getNode());
    }

    public function testCreate()
    {
        $element = Element::create('span', 'Foo', ['class' => 'bar']);

        $this->assertEquals('span', $element->tag);
        $this->assertEquals('Foo', $element->text());
        $this->assertEquals(['class' => 'bar'], $element->attributes());
    }

    public function testCreateBySelector()
    {
        $element = Element::createBySelector('li.item.active', 'Foo', ['data-id' => 1]);

        $this->assertEquals('li', $element->tag);
        $this->assertEquals('Foo', $element->text());
        $this->assertEquals(['class' => 'item active', 'data-id' => 1], $element->attributes());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrependChildWithInvalidArgument()
    {
        $element = new Element('span', 'hello');

        $element->prependChild('foo');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Can not prepend child to element without owner document
     */
    public function testPrependChildWithoutParentNode()
    {
        $element = new Element(new DOMElement('div'));

        $element->prependChild(new Element('div'));
    }

    public function testPrependChild()
    {
        $list = new Element('ul');

        $this->assertEquals(0, $list->getNode()->childNodes->length);

        $item = new Element('li', 'bar');

        $prependedChild = $list->prependChild($item);

        $this->assertEquals(1, $list->getNode()->childNodes->length);
        $this->assertInstanceOf('DiDom\Element', $prependedChild);
        $this->assertEquals('bar', $prependedChild->getNode()->textContent);

        $item = new Element('li', 'foo');

        $prependedChild = $list->prependChild($item);

        $this->assertEquals(2, $list->getNode()->childNodes->length);
        $this->assertInstanceOf('DiDom\Element', $prependedChild);
        $this->assertEquals('foo', $prependedChild->getNode()->textContent);

        $this->assertEquals('foo', $list->getNode()->childNodes->item(0)->textContent);
        $this->assertEquals('bar', $list->getNode()->childNodes->item(1)->textContent);
    }

    public function testPrependChildWithArrayOfNodes()
    {
        $list = new Element('ul');

        $prependedChild = $list->prependChild(new Element('li', 'foo'));

        $this->assertEquals(1, $list->getNode()->childNodes->length);
        $this->assertInstanceOf('DiDom\Element', $prependedChild);
        $this->assertEquals('foo', $prependedChild->getNode()->textContent);

        $items = [];

        $items[] = new Element('li', 'bar');
        $items[] = new Element('li', 'baz');

        $appendedChildren = $list->prependChild($items);

        $this->assertCount(2, $appendedChildren);
        $this->assertEquals(3, $list->getNode()->childNodes->length);

        foreach ($appendedChildren as $appendedChild) {
            $this->assertInstanceOf('DiDom\Element', $appendedChild);
        }

        foreach (['bar', 'baz', 'foo'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }
    }

    public function testPrependDocumentFragment()
    {
        $xml = '
            <list>
                <item>Foo</item>
                <item>Bar</item>
                <item>Baz</item>
            </list>
        ';

        $document = new Document();

        $document->loadXml($xml);

        $fragmentXml = '
            <item>Qux</item>
            <item>Quux</item>
            <item>Quuz</item>
        ';

        $documentFragment = $document->createDocumentFragment();

        $documentFragment->appendXml($fragmentXml);

        $document->first('list')->prependChild($documentFragment);

        $expectedContent = ['Qux', 'Quux', 'Quuz', 'Foo', 'Bar', 'Baz'];

        foreach ($document->find('item') as $index => $childNode) {
            $this->assertEquals('item', $childNode->tag);
            $this->assertEquals($expectedContent[$index], $childNode->text());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAppendChildWithInvalidArgument()
    {
        $element = new Element('span', 'hello');

        $element->appendChild('foo');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Can not append child to element without owner document
     */
    public function testAppendChildWithoutParentNode()
    {
        $element = new Element(new DOMElement('div'));

        $element->appendChild(new Element('div'));
    }

    public function testAppendChild()
    {
        $list = new Element('ul');

        $this->assertEquals(0, $list->getNode()->childNodes->length);

        $item = new Element('li', 'foo');
        $appendedChild = $list->appendChild($item);

        $this->assertEquals(1, $list->getNode()->childNodes->length);
        $this->assertInstanceOf('DiDom\Element', $appendedChild);
        $this->assertEquals('foo', $appendedChild->getNode()->textContent);

        $item = new Element('li', 'bar');
        $appendedChild = $list->appendChild($item);

        $this->assertEquals(2, $list->getNode()->childNodes->length);
        $this->assertInstanceOf('DiDom\Element', $appendedChild);
        $this->assertEquals('bar', $appendedChild->getNode()->textContent);

        $this->assertEquals('foo', $list->getNode()->childNodes->item(0)->textContent);
        $this->assertEquals('bar', $list->getNode()->childNodes->item(1)->textContent);
    }

    public function testAppendChildWithArray()
    {
        $list = new Element('ul');

        $appendedChild = $list->appendChild(new Element('li', 'foo'));

        $this->assertEquals(1, $list->getNode()->childNodes->length);
        $this->assertInstanceOf('DiDom\Element', $appendedChild);
        $this->assertEquals('foo', $appendedChild->getNode()->textContent);

        $items = [];

        $items[] = new Element('li', 'bar');
        $items[] = new Element('li', 'baz');

        $appendedChildren = $list->appendChild($items);

        $this->assertCount(2, $appendedChildren);
        $this->assertEquals(3, $list->getNode()->childNodes->length);

        foreach ($appendedChildren as $appendedChild) {
            $this->assertInstanceOf('DiDom\Element', $appendedChild);
        }

        foreach (['foo', 'bar', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }
    }

    public function testAppendDocumentFragment()
    {
        $xml = '
            <list>
                <item>Foo</item>
                <item>Bar</item>
                <item>Baz</item>
            </list>
        ';

        $document = new Document();

        $document->loadXml($xml);

        $fragmentXml = '
            <item>Qux</item>
            <item>Quux</item>
            <item>Quuz</item>
        ';

        $documentFragment = $document->createDocumentFragment();

        $documentFragment->appendXml($fragmentXml);

        $document->first('list')->appendChild($documentFragment);

        $expectedContent = ['Foo', 'Bar', 'Baz', 'Qux', 'Quux', 'Quuz'];

        foreach ($document->find('item') as $index => $childNode) {
            $this->assertEquals('item', $childNode->tag);
            $this->assertEquals($expectedContent[$index], $childNode->text());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to DiDom\Node::insertBefore must be an instance of DiDom\Node or DOMNode, string given
     */
    public function testInsertBeforeWithInvalidNodeArgument()
    {
        $list = new Element('ul');

        $list->insertBefore('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to DiDom\Node::insertBefore must be an instance of DiDom\Node or DOMNode, string given
     */
    public function testInsertBeforeWithInvalidReferenceNodeArgument()
    {
        $list = new Element('ul');

        $list->insertBefore(new Element('li', 'foo'), 'foo');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Can not insert a child to an element without the owner document
     */
    public function testInsertBeforeWithoutParentNode()
    {
        $list = new Element(new DOMElement('ul'));

        $list->insertBefore(new Element('li', 'foo'));
    }

    public function testInsertBefore()
    {
        $list = new Element('ul');

        $insertedNode = $list->insertBefore(new Element('li', 'baz'));

        $this->assertInstanceOf('DiDom\Element', $insertedNode);
        $this->assertEquals('baz', $insertedNode->getNode()->textContent);

        foreach (['baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $list->insertBefore(new Element('li', 'foo'), $list->getNode()->childNodes->item(0));

        foreach (['foo', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $list->insertBefore(new Element('li', 'bar'), $list->getNode()->childNodes->item(1));

        foreach (['foo', 'bar', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        // without the reference node
        $list->insertBefore(new Element('li', 'qux'));

        foreach (['foo', 'bar', 'baz', 'qux'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to DiDom\Node::insertBefore must be an instance of DiDom\Node or DOMNode, string given
     */
    public function testInsertAfterWithInvalidNodeArgument()
    {
        $list = new Element('ul');

        $list->insertAfter('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to DiDom\Node::insertAfter must be an instance of DiDom\Node or DOMNode, string given
     */
    public function testInsertAfterWithInvalidReferenceNodeArgument()
    {
        $list = new Element('ul');

        $list->insertAfter(new Element('li', 'foo'), 'foo');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Can not insert a child to an element without the owner document
     */
    public function testInsertAfterWithoutParentNode()
    {
        $list = new Element(new DOMElement('ul'));

        $list->insertAfter(new Element('li', 'foo'));
    }

    public function testInsertAfter()
    {
        $list = new Element('ul');

        $insertedNode = $list->insertAfter(new Element('li', 'foo'));

        $this->assertInstanceOf('DiDom\Element', $insertedNode);
        $this->assertEquals('foo', $insertedNode->getNode()->textContent);

        foreach (['foo'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $list->insertAfter(new Element('li', 'baz'), $list->getNode()->childNodes->item(0));

        foreach (['foo', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $list->insertAfter(new Element('li', 'bar'), $list->getNode()->childNodes->item(0));

        foreach (['foo', 'bar', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        // without the reference node
        $list->insertAfter(new Element('li', 'qux'));

        foreach (['foo', 'bar', 'baz', 'qux'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to DiDom\Node::insertSiblingBefore must be an instance of DiDom\Node or DOMNode, string given
     */
    public function testInsertSiblingBeforeWithInvalidNodeArgument()
    {
        $list = new Element('ul');

        $item = $list->appendChild(new Element('li', 'foo'));

        $item->insertSiblingBefore('foo');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Can not insert a child to an element without the owner document
     */
    public function testInsertSiblingBeforeWithoutParentNode()
    {
        $item = new Element(new DOMElement('li', 'foo'));

        $item->insertSiblingBefore(new Element('li', 'bar'));
    }

    public function testInsertSiblingBefore()
    {
        $list = new Element('ul');

        $insertedNode = $list->appendChild(new Element('li', 'baz'));

        $this->assertInstanceOf('DiDom\Element', $insertedNode);
        $this->assertEquals('baz', $insertedNode->getNode()->textContent);

        foreach (['baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $insertedNode->insertSiblingBefore(new Element('li', 'foo'));

        foreach (['foo', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $insertedNode->insertSiblingBefore(new Element('li', 'bar'));

        foreach (['foo', 'bar', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to DiDom\Node::appendChild must be an instance of DiDom\Node or DOMNode, string given
     */
    public function testInsertSiblingAfterWithInvalidNodeArgument()
    {
        $list = new Element('ul');

        $item = $list->appendChild(new Element('li', 'foo'));

        $item->insertSiblingAfter('foo');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Can not insert a child to an element without the owner document
     */
    public function testInsertSiblingAfterWithoutParentNode()
    {
        $item = new Element(new DOMElement('li', 'foo'));

        $item->insertSiblingAfter(new Element('li', 'bar'));
    }

    public function testInsertSiblingAfter()
    {
        $list = new Element('ul');

        $insertedNode = $list->appendChild(new Element('li', 'foo'));

        $this->assertInstanceOf('DiDom\Element', $insertedNode);
        $this->assertEquals('foo', $insertedNode->getNode()->textContent);

        foreach (['foo'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $insertedNode->insertSiblingAfter(new Element('li', 'baz'));

        foreach (['foo', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }

        $insertedNode->insertSiblingAfter(new Element('li', 'bar'));

        foreach (['foo', 'bar', 'baz'] as $index => $value) {
            $this->assertEquals($value, $list->getNode()->childNodes->item($index)->textContent);
        }
    }

    public function testHas()
    {
        $document = new DOMDocument();
        $document->loadHTML('<div><span class="foo">bar</span></div>');

        $node = $document->getElementsByTagName('div')->item(0);
        $element = new Element($node);

        $this->assertTrue($element->has('.foo'));
        $this->assertFalse($element->has('.bar'));
    }

    /**
     * @dataProvider findTests
     */
    public function testFind($html, $selector, $type, $count)
    {
        $document = new DOMDocument();
        $document->loadHTML($html);

        $domElement = $document->getElementsByTagName('body')->item(0);
        $element = new Element($domElement);

        $elements = $element->find($selector, $type);

        $this->assertTrue(is_array($elements));
        $this->assertEquals($count, count($elements));

        foreach ($elements as $element) {
            $this->assertInstanceOf('DiDom\Element', $element);
        }
    }

    /**
     * @expectedException LogicException
     */
    public function testFindInDocumentWithoutOwnerDocument()
    {
        $element = new Element(new DOMElement('div'));

        $element->findInDocument('.foo');
    }

    public function testFindInDocument()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html);

        $items = $document->find('li');
        $list = $document->first('ul');

        foreach ($list->find('li') as $index => $item) {
            $this->assertFalse($item->is($items[$index]));
        }

        foreach ($list->findInDocument('li') as $index => $item) {
            $this->assertTrue($item->is($items[$index]));
        }

        $this->assertCount(3, $document->find('li'));

        $list->findInDocument('li')[0]->remove();

        $this->assertCount(2, $document->find('li'));
    }

    /**
     * @expectedException LogicException
     */
    public function testFirstInDocumentWithoutOwnerDocument()
    {
        $element = new Element(new DOMElement('div'));

        $element->firstInDocument('.foo');
    }

    public function testFirstInDocument()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html);

        $item = $document->first('li');
        $list = $document->first('ul');

        $this->assertFalse($item->is($list->first('li')));
        $this->assertTrue($item->is($list->firstInDocument('li')));

        $this->assertCount(3, $document->find('li'));

        $list->findInDocument('li')[0]->remove();

        $this->assertCount(2, $document->find('li'));
    }

    public function testFirst()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(0);

        $this->assertEquals($item, $list->first('li')->getNode());

        $list = new Element('ul');

        $this->assertNull($list->first('li'));
    }

    /**
     * @dataProvider findTests
     */
    public function testFindAndReturnDomElement($html, $selector, $type, $count)
    {
        $document = new DOMDocument();
        $document->loadHTML($html);

        $node = $document->getElementsByTagName('body')->item(0);
        $element = new Element($node);

        $elements = $element->find($selector, $type, false);

        $this->assertTrue(is_array($elements));
        $this->assertEquals($count, count($elements));

        foreach ($elements as $element) {
            $this->assertInstanceOf('DOMElement', $element);
        }
    }

    public function findTests()
    {
        $html = $this->loadFixture('posts.html');

        return array(
            array($html, '.post h2', Query::TYPE_CSS, 3),
            array($html, '.fake h2', Query::TYPE_CSS, 0),
            array($html, '.post h2, .post p', Query::TYPE_CSS, 6),
            array($html, "//*[contains(concat(' ', normalize-space(@class), ' '), ' post ')]", Query::TYPE_XPATH, 3),
        );
    }

    public function testXpath()
    {
        $html = $this->loadFixture('posts.html');

        $document = new DOMDocument();
        $document->loadHTML($html);

        $node = $document->getElementsByTagName('body')->item(0);
        $element = new Element($node);

        $elements = $element->xpath("//*[contains(concat(' ', normalize-space(@class), ' '), ' post ')]");

        $this->assertTrue(is_array($elements));
        $this->assertEquals(3, count($elements));

        foreach ($elements as $element) {
            $this->assertInstanceOf('DiDom\Element', $element);
        }
    }

    public function testCount()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);
        $list = $document->first('ul');

        $this->assertEquals(3, $list->count('li'));

        $document = new Element('ul');

        $this->assertEquals(0, $document->count('li'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMatchesWithInvalidSelectorType()
    {
        $element = new Element('p');

        $element->matches(null);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testMatchesStrictWithoutTagName()
    {
        $element = new Element('ul', null, ['id' => 'foo', 'class' => 'bar baz']);

        $element->matches('#foo.bar.baz', true);
    }

    public function testMatches()
    {
        $element = new Element('ul', null, ['id' => 'foo', 'class' => 'bar baz']);

        $this->assertTrue($element->matches('ul'));
        $this->assertTrue($element->matches('#foo'));
        $this->assertTrue($element->matches('.bar'));
        $this->assertTrue($element->matches('ul#foo.bar.baz'));
        $this->assertFalse($element->matches('a#foo.bar.baz'));

        // strict
        $this->assertTrue($element->matches('ul#foo.bar.baz', true));
        $this->assertFalse($element->matches('ul#foo.bar', true));
        $this->assertFalse($element->matches('ul#foo', true));
        $this->assertFalse($element->matches('ul.bar.baz', true));
        $this->assertFalse($element->matches('ul.bar.baz', true));

        $element = new Element('p');

        $this->assertTrue($element->matches('p', true));

        $html = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Document</title>
        </head>
        <body>
            <a href="#"><img src="foo.jpg" alt="Foo"></a>
        </body>
        </html>';

        $document = new Document($html, false);
        $anchor = $document->first('a');

        $this->assertTrue($anchor->matches('a:has(img[src$=".jpg"])'));
        $this->assertTrue($anchor->matches('a img'));
        $this->assertFalse($anchor->matches('a img[alt="Bar"]'));
        $this->assertFalse($anchor->matches('img'));

        $textNode = new DOMText('Foo');
        $element = new Element($textNode);

        $this->assertFalse($element->matches('#foo'));

        $commentNode = new DOMComment('Foo');
        $element = new Element($commentNode);

        $this->assertFalse($element->matches('#foo'));
    }

    public function testHasAttribute()
    {
        $node = $this->createDomElement('input');
        $element = new Element($node);

        $this->assertFalse($element->hasAttribute('value'));

        $node->setAttribute('value', 'test');

        $this->assertTrue($element->hasAttribute('value'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetAttributeWithInvalidValue()
    {
        $element = new Element('input');
        $element->setAttribute('value', []);
    }

    public function testSetAttribute()
    {
        $node = $this->createDomElement('input');

        $element = new Element($node);

        $element->setAttribute('value', 'foo');
        $this->assertEquals('foo', $element->getNode()->getAttribute('value'));

        $element->setAttribute('value', 10);
        $this->assertEquals('10', $element->getNode()->getAttribute('value'));

        $element->setAttribute('value', 3.14);
        $this->assertEquals('3.14', $element->getNode()->getAttribute('value'));
    }

    public function testGetAttribute()
    {
        $node = $this->createDomElement('input');

        $element = new Element($node);

        $this->assertEquals(null, $element->getAttribute('value'));
        $this->assertEquals('default', $element->getAttribute('value', 'default'));

        $node->setAttribute('value', 'test');

        $this->assertEquals('test', $element->getAttribute('value'));
    }

    public function testRemoveAttribute()
    {
        $domElement = $this->createDomElement('input', null, ['name' => 'username']);

        $element = new Element($domElement);

        $this->assertTrue($element->getNode()->hasAttribute('name'));

        $result = $element->removeAttribute('name');

        $this->assertEquals(0, $element->getNode()->attributes->length);
        $this->assertFalse($element->getNode()->hasAttribute('name'));
        $this->assertEquals($result, $element);
    }

    public function testRemoveAllAttributes()
    {
        $attributes = ['type' => 'text', 'name' => 'username'];

        $domElement = $this->createDomElement('input', null, $attributes);

        $element = new Element($domElement);

        $result = $element->removeAllAttributes();

        $this->assertEquals(0, $element->getNode()->attributes->length);
        $this->assertEquals($result, $element);
    }

    public function testRemoveAllAttributesWithExclusion()
    {
        $attributes = ['type' => 'text', 'name' => 'username'];

        $domElement = $this->createDomElement('input', null, $attributes);

        $element = new Element($domElement);

        $element->removeAllAttributes(['name']);

        $this->assertEquals(1, $element->getNode()->attributes->length);
        $this->assertEquals('username', $element->getNode()->getAttribute('name'));
    }

    public function testAttrSet()
    {
        $element = new Element('input');

        $element->attr('name', 'username');

        $this->assertEquals('username', $element->getNode()->getAttribute('name'));
    }

    public function testAttrGet()
    {
        $element = new Element('input', null, ['name' => 'username']);

        $this->assertEquals('username', $element->attr('name'));
    }

    public function testAttributes()
    {
        $attributes = ['type' => 'text', 'name' => 'username', 'value' => 'John'];

        $domElement = $this->createDomElement('input', null, $attributes);

        $element = new Element($domElement);

        $this->assertEquals($attributes, $element->attributes());
        $this->assertEquals(['name' => 'username', 'value' => 'John'], $element->attributes(['name', 'value']));
    }


    public function testAttributesWithText()
    {
        $element = new Element(new DOMText('Foo'));

        $this->assertNull($element->attributes());
    }


    public function testAttributesWithComment()
    {
        $element = new Element(new DOMComment('Foo'));

        $this->assertNull($element->attributes());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Style attribute is available only for element nodes
     */
    public function testStyleWithTextNode()
    {
        $element = new Element(new DOMText('foo'));

        $element->style();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Style attribute is available only for element nodes
     */
    public function testStyleWithCommentNode()
    {
        $element = new Element(new DOMComment('foo'));

        $element->style();
    }

    public function testStyle()
    {
        $element = new Element('div');

        $styleAttribute = $element->style();

        $this->assertInstanceOf('DiDom\\StyleAttribute', $styleAttribute);
        $this->assertSame($element, $styleAttribute->getElement());

        $this->assertSame($styleAttribute, $element->style());

        $element2 = new Element('div');

        $this->assertNotSame($element->style(), $element2->style());
    }

    public function testHtml()
    {
        $element = new Element('span', 'hello');

        $this->assertEquals('<span>hello</span>', $element->html());
    }

    public function testOuterHtml()
    {
        $innerHtml = 'Plain text <span>Lorem ipsum.</span><span>Lorem ipsum.</span>';
        $html = "<div id=\"foo\" class=\"bar baz\">$innerHtml</div>";

        $document = new Document($html);

        $this->assertEquals('<div id="foo" class="bar baz"></div>', $document->first('#foo')->outerHtml());
    }

    public function testInnerHtml()
    {
        $innerHtml = ' Plain text <span>Lorem ipsum.</span><span>Lorem ipsum.</span>';
        $html = "<div id=\"root\">$innerHtml</div>";

        $document = new Document($html);

        $this->assertEquals($innerHtml, $document->first('#root')->innerHtml());

        $html = '
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Document</title>
</head>
<body>
English language <br>
Русский язык <br>
اللغة العربية <br>
漢語 <br>
Tiếng Việt <br>

&lt; &gt;
</body>
</html>
        ';

        $expectedContent = '
English language <br>
Русский язык <br>
اللغة العربية <br>
漢語 <br>
Tiếng Việt <br>

&lt; &gt;
';

        $document = new Document($html);

        $this->assertEquals($expectedContent, $document->first('body')->innerHtml());
    }

    public function testInnerHtmlOnXmlElement()
    {
        $innerXml = 'Plain text <span>Lorem <single-tag/> ipsum.</span><span>Lorem ipsum.</span>';
        $xml = "<div id=\"root\">$innerXml</div>";

        $document = new Document($xml, false, 'UTF-8', Document::TYPE_XML);

        $expectedXml = 'Plain text <span>Lorem <single-tag></single-tag> ipsum.</span><span>Lorem ipsum.</span>';

        $this->assertEquals($expectedXml, $document->first('#root')->innerHtml());
    }

    public function testInnerXml()
    {
        $innerXml = 'Plain text <span>Lorem <single-tag/> ipsum.</span><span>Lorem ipsum.</span>';
        $xml = "<div id=\"root\">$innerXml</div>";

        $document = new Document($xml, false, 'UTF-8', Document::TYPE_XML);

        $this->assertEquals($innerXml, $document->first('#root')->innerXml());
    }

    public function testSetInnerHtml()
    {
        $list = new Element('ul');

        $html = '<li>One</li><li>Two</li><li>Three</li>';

        $this->assertEquals($list, $list->setInnerHtml($html));
        $this->assertEquals(['One', 'Two', 'Three'], $list->find('li::text'));

        // check inner HTML rewrite works

        $html = '<li>Foo</li><li>Bar</li><li>Baz</li>';

        $this->assertEquals($list, $list->setInnerHtml($html));
        $this->assertEquals(['Foo', 'Bar', 'Baz'], $list->find('li::text'));

        $html = '<div id="root"></div>';
        $innerHtml = ' Plain text <span>Lorem ipsum.</span><span>Lorem ipsum.</span>';

        $document = new Document($html, false);

        $document->first('#root')->setInnerHtml($innerHtml);

        $this->assertEquals($innerHtml, $document->first('#root')->innerHtml());
    }

    public function testXml()
    {
        $element = new Element('span', 'hello');

        $prolog = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

        $this->assertEquals($prolog.'<span>hello</span>', $element->xml());
    }

    public function testXmlWithOptions()
    {
        $html = '<html><body><span></span></body></html>';

        $document = new Document();
        $document->loadHtml($html);

        $element = $document->find('span')[0];

        $prolog = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

        $this->assertEquals($prolog.'<span/>', $element->xml());
        $this->assertEquals($prolog.'<span></span>', $element->xml(LIBXML_NOEMPTYTAG));
    }

    public function testGetText()
    {
        $element = new Element('span', 'hello');

        $this->assertEquals('hello', $element->text());
    }

    public function testSetValue()
    {
        $element = new Element('span', 'hello');
        $element->setValue('test');

        $this->assertEquals('test', $element->text());
    }

    public function testIsElementNode()
    {
        $element = new Element('div');

        $element->setInnerHtml(' Foo <span>Bar</span><!-- Baz -->');

        $children = $element->children();

        $this->assertFalse($children[0]->isElementNode());
        $this->assertTrue($children[1]->isElementNode());
        $this->assertFalse($children[2]->isElementNode());
    }

    public function testIsTextNode()
    {
        $element = new Element('div');

        $element->setInnerHtml(' Foo <span>Bar</span><!-- Baz -->');

        $children = $element->children();

        $this->assertTrue($children[0]->isTextNode());
        $this->assertFalse($children[1]->isTextNode());
        $this->assertFalse($children[2]->isTextNode());
    }

    public function testIsCommentNode()
    {
        $element = new Element('div');

        $element->setInnerHtml(' Foo <span>Bar</span><!-- Baz -->');

        $children = $element->children();

        $this->assertFalse($children[0]->isCommentNode());
        $this->assertFalse($children[1]->isCommentNode());
        $this->assertTrue($children[2]->isCommentNode());
    }

    public function testIs()
    {
        $element  = new Element('span', 'hello');
        $element2 = new Element('span', 'hello');

        $this->assertTrue($element->is($element));
        $this->assertFalse($element->is($element2));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIsWithInvalidArgument()
    {
        $element = new Element('span', 'hello');
        $element->is(null);
    }

    public function testParent()
    {
        $html = $this->loadFixture('posts.html');
        $document = new Document($html, false);
        $element = $document->createElement('span', 'value');

        $this->assertEquals($document->getDocument(), $element->getDocument()->getDocument());
    }

    public function testClosest()
    {
        // without body and html tags
        $html = '
            <nav>
                <ul class="menu">
                    <li><a href="#">Foo</a></li>
                    <li><a href="#">Bar</a></li>
                    <li><a href="#">Baz</a></li>
                </ul>
            </nav>
        ';

        $document = new Document($html);

        $menu = $document->first('.menu');
        $link = $document->first('a');

        $this->assertNull($link->closest('.unknown-class'));
        $this->assertEquals($menu, $link->closest('.menu'));

        $html = '<!DOCTYPE html>
        <html>
        <body>
            <nav></nav>
            <ul class="menu">
                <li><a href="#">Foo</a></li>
                <li><a href="#">Bar</a></li>
                <li><a href="#">Baz</a></li>
            </ul>
        </body>
        </html>';

        $document = new Document($html);

        $this->assertNull($document->first('ul.menu')->closest('nav'));
    }

    // =========================
    // previousSibling
    // =========================

    public function testPreviousSibling()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $list = $document->first('ul')->getNode();

        $item = $list->childNodes->item(0);
        $item = new Element($item);

        $this->assertNull($item->previousSibling());

        $item = $list->childNodes->item(1);
        $item = new Element($item);

        $expectedNode = $list->childNodes->item(0);

        $this->assertEquals($expectedNode, $item->previousSibling()->getNode());
    }

    public function testPreviousSiblingWithTextNode()
    {
        $html = '<p>Foo <span>Bar</span> Baz</p>';

        $document = new Document($html, false);

        $span = $document->first('span');

        $expectedNode = $span->getNode()->previousSibling;

        $this->assertEquals($expectedNode, $span->previousSibling()->getNode());
    }

    public function testPreviousSiblingWithCommentNode()
    {
        $html = '<p><!-- Foo --><span>Bar</span> Baz</p>';

        $document = new Document($html, false);

        $span = $document->first('span');

        $expectedNode = $span->getNode()->previousSibling;

        $this->assertEquals($expectedNode, $span->previousSibling()->getNode());
    }

    public function testPreviousSiblingWithSelector()
    {
        $html =
            '<ul>'.
                '<li><a href="https://amazon.com">Amazon</a></li>'.
                '<li><a href="https://facebook.com">Facebook</a></li>'.
                '<li><a href="https://google.com">Google</a></li>'.
                '<li><a href="https://www.w3.org">W3C</a></li>'.
                '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(4);
        $item = new Element($item);

        $expectedNode = $list->getNode()->childNodes->item(2);

        $this->assertEquals($expectedNode, $item->previousSibling('li:has(a[href$=".com"])')->getNode());
    }

    public function testPreviousSiblingWithNodeType()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $document->find('span')[1];

        $expectedNode = $paragraph->getNode()->childNodes->item(1);
        $this->assertEquals($expectedNode, $span->previousSibling(null, 'DOMElement')->getNode());

        $expectedNode = $paragraph->getNode()->childNodes->item(2);
        $this->assertEquals($expectedNode, $span->previousSibling(null, 'DOMComment')->getNode());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPreviousSiblingWithInvalidTypeOfNodeTypeArgument()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[1];

        $span->previousSibling(null, []);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testPreviousSiblingWithInvalidNodeType()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[1];

        $span->previousSibling(null, 'foo');
    }

    /**
     * @dataProvider previousSiblingWithSelectorAndNotDomElementNodeTypeDataProvider
     *
     * @expectedException LogicException
     *
     * @param string $nodeType
     */
    public function testPreviousSiblingWithSelectorAndNotDomElement($nodeType)
    {
        $html =
            '<ul>'.
                '<li><a href="https://amazon.com">Amazon</a></li>'.
                '<li><a href="https://facebook.com">Facebook</a></li>'.
                '<li><a href="https://google.com">Google</a></li>'.
                '<li><a href="https://www.w3.org">W3C</a></li>'.
                '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(4);
        $item = new Element($item);

        $item->previousSibling('li:has(a[href$=".com"])', $nodeType);
    }

    public function previousSiblingWithSelectorAndNotDomElementNodeTypeDataProvider()
    {
        return [['DOMText'], ['DOMComment']];
    }

    // =========================
    // previousSiblings
    // =========================

    public function testPreviousSiblings()
    {
        $html = '<p>Foo <span>Bar</span> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $paragraph->find('span')[1];

        $childNodes = $paragraph->getNode()->childNodes;

        $expectedResult = [
            $childNodes->item(0),
            $childNodes->item(1),
            $childNodes->item(2),
        ];

        $previousSiblings = $span->previousSiblings();

        $this->assertCount(count($expectedResult), $previousSiblings);

        foreach ($previousSiblings as $index => $previousSibling) {
            $this->assertEquals($expectedResult[$index], $previousSibling->getNode());
        }
    }

    public function testPreviousSiblingsWithSelector()
    {
        $html =
            '<ul>'.
                '<li><a href="https://amazon.com">Amazon</a></li>'.
                '<li><a href="https://facebook.com">Facebook</a></li>'.
                '<li><a href="https://google.com">Google</a></li>'.
                '<li><a href="https://www.w3.org">W3C</a></li>'.
                '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(4);
        $item = new Element($item);

        $childNodes = $list->getNode()->childNodes;

        $expectedResult = [
            $childNodes->item(0),
            $childNodes->item(1),
            $childNodes->item(2),
        ];

        $previousSiblings = $item->previousSiblings('li:has(a[href$=".com"])');

        $this->assertCount(count($expectedResult), $previousSiblings);

        foreach ($previousSiblings as $index => $previousSibling) {
            $this->assertEquals($expectedResult[$index], $previousSibling->getNode());
        }
    }

    public function testPreviousSiblingsWithNodeType()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $document->find('span')[1];

        $childNodes = $paragraph->getNode()->childNodes;

        $expectedResult = [
            $childNodes->item(1),
        ];

        $previousSiblings = $span->previousSiblings(null, 'DOMElement');

        $this->assertCount(count($expectedResult), $previousSiblings);

        foreach ($previousSiblings as $index => $previousSibling) {
            $this->assertEquals($expectedResult[$index], $previousSibling->getNode());
        }

        $expectedResult = [
            $childNodes->item(2),
        ];

        $previousSiblings = $span->previousSiblings(null, 'DOMComment');

        $this->assertCount(count($expectedResult), $previousSiblings);

        foreach ($previousSiblings as $index => $previousSibling) {
            $this->assertEquals($expectedResult[$index], $previousSibling->getNode());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPreviousSiblingsWithInvalidTypeOfNodeTypeArgument()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[1];

        $span->previousSiblings(null, []);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testPreviousSiblingsWithInvalidNodeType()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[1];

        $span->previousSibling(null, 'foo');
    }

    /**
     * @dataProvider previousSiblingsWithSelectorAndNotDomElementNodeTypeDataProvider
     *
     * @expectedException LogicException
     *
     * @param string $nodeType
     */
    public function testPreviousSiblingsWithSelectorAndNotDomElement($nodeType)
    {
        $html =
            '<ul>'.
            '<li><a href="https://amazon.com">Amazon</a></li>'.
            '<li><a href="https://facebook.com">Facebook</a></li>'.
            '<li><a href="https://google.com">Google</a></li>'.
            '<li><a href="https://www.w3.org">W3C</a></li>'.
            '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(4);
        $item = new Element($item);

        $item->previousSiblings('li:has(a[href$=".com"])', $nodeType);
    }

    public function previousSiblingsWithSelectorAndNotDomElementNodeTypeDataProvider()
    {
        return [['DOMText'], ['DOMComment']];
    }

    // =========================
    // nextSibling
    // =========================

    public function testNextSibling()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(2);
        $item = new Element($item);

        $this->assertNull($item->nextSibling());

        $item = $list->getNode()->childNodes->item(0);
        $item = new Element($item);

        $expectedNode = $list->getNode()->childNodes->item(1);

        $this->assertEquals($expectedNode, $item->nextSibling()->getNode());
    }

    public function testNextSiblingWithTextNode()
    {
        $html = '<p>Foo <span>Bar</span> Baz</p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $paragraph->first('span');

        $expectedNode = $span->getNode()->nextSibling;

        $this->assertEquals($expectedNode, $span->nextSibling()->getNode());
    }

    public function testNextSiblingWithCommentNode()
    {
        $html = '<p>Foo <span>Bar</span><!-- Baz --></p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $paragraph->first('span');

        $expectedNode = $span->getNode()->nextSibling;

        $this->assertEquals($expectedNode, $span->nextSibling()->getNode());
    }

    public function testNextSiblingWithSelector()
    {
        $html =
            '<ul>'.
                '<li><a href="https://amazon.com">Amazon</a></li>'.
                '<li><a href="https://facebook.com">Facebook</a></li>'.
                '<li><a href="https://google.com">Google</a></li>'.
                '<li><a href="https://www.w3.org">W3C</a></li>'.
                '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(0);
        $item = new Element($item);

        $expectedNode = $list->getNode()->childNodes->item(3);

        $this->assertEquals($expectedNode, $item->nextSibling('li:has(a[href$=".org"])')->getNode());
    }

    public function testNextSiblingWithNodeType()
    {
        $html = '<p>Foo <span>Bar</span> Baz <!--qwe--><span>Qux</span></p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $document->find('span')[0];

        $expectedNode = $paragraph->getNode()->childNodes->item(4);
        $this->assertEquals($expectedNode, $span->nextSibling(null, 'DOMElement')->getNode());

        $expectedNode = $paragraph->getNode()->childNodes->item(3);
        $this->assertEquals($expectedNode, $span->nextSibling(null, 'DOMComment')->getNode());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNextSiblingWithInvalidTypeOfNodeTypeArgument()
    {
        $html = '<p>Foo <span>Bar</span> Baz <!--qwe--><span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[0];

        $span->nextSibling(null, []);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNextSiblingWithInvalidNodeType()
    {
        $html = '<p>Foo <span>Bar</span> Baz <!--qwe--><span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[0];

        $span->nextSibling(null, 'foo');
    }

    /**
     * @dataProvider nextSiblingWithSelectorAndNotDomElementNodeTypeDataProvider
     *
     * @expectedException LogicException
     *
     * @param string $nodeType
     */
    public function testNextSiblingWithSelectorAndNotDomElement($nodeType)
    {
        $html =
            '<ul>'.
                '<li><a href="https://amazon.com">Amazon</a></li>'.
                '<li><a href="https://facebook.com">Facebook</a></li>'.
                '<li><a href="https://google.com">Google</a></li>'.
                '<li><a href="https://www.w3.org">W3C</a></li>'.
                '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(0);
        $item = new Element($item);

        $item->nextSibling('li:has(a[href$=".com"])', $nodeType);
    }

    public function nextSiblingWithSelectorAndNotDomElementNodeTypeDataProvider()
    {
        return [['DOMText'], ['DOMComment']];
    }

    // =========================
    // nextSiblings
    // =========================

    public function testNextSiblings()
    {
        $html = '<p>Foo <span>Bar</span> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $paragraph->find('span')[0];

        $childNodes = $paragraph->getNode()->childNodes;

        $expectedResult = [
            $childNodes->item(2),
            $childNodes->item(3),
        ];

        $nextSiblings = $span->nextSiblings();

        $this->assertCount(count($expectedResult), $nextSiblings);

        foreach ($nextSiblings as $index => $nextSibling) {
            $this->assertEquals($expectedResult[$index], $nextSibling->getNode());
        }
    }

    public function testNextSiblingsWithSelector()
    {
        $html =
            '<ul>'.
                '<li><a href="https://amazon.com">Amazon</a></li>'.
                '<li><a href="https://facebook.com">Facebook</a></li>'.
                '<li><a href="https://google.com">Google</a></li>'.
                '<li><a href="https://www.w3.org">W3C</a></li>'.
                '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(0);
        $item = new Element($item);

        $childNodes = $list->getNode()->childNodes;

        $expectedResult = [
            $childNodes->item(1),
            $childNodes->item(2),
        ];

        $nextSiblings = $item->nextSiblings('li:has(a[href$=".com"])');

        $this->assertCount(count($expectedResult), $nextSiblings);

        foreach ($nextSiblings as $index => $nextSibling) {
            $this->assertEquals($expectedResult[$index], $nextSibling->getNode());
        }
    }

    public function testNextSiblingsWithNodeType()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');
        $span = $document->find('span')[0];

        $childNodes = $paragraph->getNode()->childNodes;

        $expectedResult = [
            $childNodes->item(4),
        ];

        $previousSiblings = $span->nextSiblings(null, 'DOMElement');

        $this->assertCount(count($expectedResult), $previousSiblings);

        foreach ($previousSiblings as $index => $previousSibling) {
            $this->assertEquals($expectedResult[$index], $previousSibling->getNode());
        }

        $expectedResult = [
            $childNodes->item(2),
        ];

        $previousSiblings = $span->nextSiblings(null, 'DOMComment');

        $this->assertCount(count($expectedResult), $previousSiblings);

        foreach ($previousSiblings as $index => $previousSibling) {
            $this->assertEquals($expectedResult[$index], $previousSibling->getNode());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNextSiblingsWithInvalidTypeOfNodeTypeArgument()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[0];

        $span->nextSiblings(null, []);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNextSiblingsWithInvalidNodeType()
    {
        $html = '<p>Foo <span>Bar</span><!--qwe--> Baz <span>Qux</span></p>';

        $document = new Document($html, false);

        $span = $document->find('span')[0];

        $span->nextSiblings(null, 'foo');
    }

    /**
     * @dataProvider nextSiblingsWithSelectorAndNotDomElementNodeTypeDataProvider
     *
     * @expectedException LogicException
     *
     * @param string $nodeType
     */
    public function testNextSiblingsWithSelectorAndNotDomElement($nodeType)
    {
        $html =
            '<ul>'.
                '<li><a href="https://amazon.com">Amazon</a></li>'.
                '<li><a href="https://facebook.com">Facebook</a></li>'.
                '<li><a href="https://google.com">Google</a></li>'.
                '<li><a href="https://www.w3.org">W3C</a></li>'.
                '<li><a href="https://wikipedia.org">Wikipedia</a></li>'.
            '</ul>'
        ;

        $document = new Document($html, false);

        $list = $document->first('ul');

        $item = $list->getNode()->childNodes->item(0);
        $item = new Element($item);

        $item->nextSiblings('li:has(a[href$=".com"])', $nodeType);
    }

    public function nextSiblingsWithSelectorAndNotDomElementNodeTypeDataProvider()
    {
        return [['DOMText'], ['DOMComment']];
    }

    public function testChild()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $list = $document->first('ul');

        $this->assertEquals($list->getNode()->childNodes->item(0), $list->child(0)->getNode());
        $this->assertEquals($list->getNode()->childNodes->item(2), $list->child(2)->getNode());
        $this->assertNull($list->child(3));

        // with text nodes
        $html = '<p>Foo <span>Bar</span> Baz</p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');

        $child = $paragraph->getNode()->childNodes->item(0);

        $this->assertEquals($child, $paragraph->child(0)->getNode());
    }

    public function testFirstChild()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $list = $document->first('ul');

        $this->assertEquals($list->getNode()->firstChild, $list->firstChild()->getNode());

        $list = new Element('ul');

        $this->assertNull($list->firstChild());

        // with text nodes
        $html = '<p>Foo <span>Bar</span> Baz</p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');

        $firstChild = $paragraph->getNode()->firstChild;

        $this->assertEquals($firstChild, $paragraph->firstChild()->getNode());
    }

    public function testLastChild()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);
        $list = $document->first('ul');

        $this->assertEquals($list->getNode()->lastChild, $list->lastChild()->getNode());

        $list = new Element('ul');

        $this->assertNull($list->lastChild());

        // with text nodes
        $html = '<p>Foo <span>Bar</span> Baz</p>';

        $document = new Document($html, false);
        $paragraph = $document->first('p');

        $lastChild = $paragraph->getNode()->lastChild;

        $this->assertEquals($lastChild, $paragraph->lastChild()->getNode());
    }

    public function testHasChildren()
    {
        $html = '
            <p class="element"><br></p>
            <p class="text">Foo</p>
            <p class="comment"><!-- Foo --></p>
            <p class="empty"></p>
        ';

        $document = new Document($html);

        $this->assertTrue($document->first('.element')->hasChildren());
        $this->assertTrue($document->first('.text')->hasChildren());
        $this->assertTrue($document->first('.comment')->hasChildren());
        $this->assertFalse($document->first('.empty')->hasChildren());
    }

    public function testChildren()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $list = $document->first('ul');

        $children = $list->children();

        foreach ($list->getNode()->childNodes as $index => $node) {
            $this->assertEquals($node, $children[$index]->getNode());
        }

        // with text nodes
        $html = '<p>Foo <span>Bar</span> Baz</p>';

        $document = new Document($html, false);

        $paragraph = $document->first('p');

        $children = $paragraph->children();

        foreach ($paragraph->getNode()->childNodes as $index => $node) {
            $this->assertEquals($node, $children[$index]->getNode());
        }
    }

    public function testParentWithoutOwner()
    {
        $element = new Element(new DOMElement('span', 'hello'));

        $this->assertNull($element->parent());
    }

    public function testRemoveChild()
    {
        $html = '<div><span>Foo</span></div>';
        $document = new Document($html, false);

        $div = $document->first('div');
        $span = $document->first('span');

        $this->assertEquals($span->getNode(), $div->removeChild($span)->getNode());
        $this->assertCount(0, $document->find('span'));
    }

    public function testRemoveChildren()
    {
        $html = '<div><span>Foo</span>Bar<!-- Baz --></div>';
        $document = new Document($html, false);

        $div = $document->first('div');
        $span = $document->first('span');

        $childNodes = $div->children();
        $removedNodes = $div->removeChildren();

        foreach ($childNodes as $index => $childNode) {
            $this->assertEquals($childNode->getNode(), $removedNodes[$index]->getNode());
        }

        $this->assertCount(0, $document->find('span'));
    }

    public function testRemove()
    {
        $html = '<div><span>Foo</span></div>';
        $document = new Document($html, false);

        $span = $document->first('span');

        $this->assertEquals($span->getNode(), $span->remove()->getNode());
        $this->assertCount(0, $document->find('span'));
    }

    /**
     * @expectedException LogicException
     */
    public function testRemoveWithoutParentNode()
    {
        $element = new Element('div', 'Foo');

        $element->remove();
    }

    public function testReplace()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $first = $document->find('li')[0];
        $third = $document->find('li')[2];

        $this->assertEquals($first->getNode(), $first->replace($third)->getNode());
        $this->assertEquals($third->getNode(), $document->find('li')[0]->getNode());
        $this->assertCount(3, $document->find('li'));

        // replace without cloning
        $document = new Document($html, false);

        $first = $document->find('li')[0];
        $third = $document->find('li')[2];

        $this->assertEquals($first->getNode(), $first->replace($third, false)->getNode());
        $this->assertEquals($third->getNode(), $document->find('li')[0]->getNode());
        $this->assertCount(2, $document->find('li'));
    }

    public function testReplaceWithNewElement()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $first = $document->find('li')[0];

        $newElement = new Element('li', 'Foo');

        $this->assertEquals($first->getNode(), $first->replace($newElement)->getNode());
        $this->assertEquals('Foo', $document->find('li')[0]->text());
        $this->assertCount(3, $document->find('li'));

        // replace with new node
        $html = '<span>Foo <a href="#">Bar</a> Baz</span>';

        $document = new Document($html, false);

        $anchor = $document->first('a');

        $textNode = new DOMText($anchor->text());

        $anchor->replace($textNode);
    }

    public function testReplaceWithElementFromAnotherDocument()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);
        $document2 = new Document($html, false);

        $first = $document->find('li')[0];
        $third = $document2->find('li')[2];

        $first->replace($third);
    }

    public function testReplaceWithDocumentFragment()
    {
        $xml = '
            <list>
                <item>Foo</item>
                <item>Bar</item>
                <item>Baz</item>
            </list>
        ';

        $document = new Document();

        $document->loadXml($xml);

        $fragmentXml = '
            <item>Qux</item>
            <item>Quux</item>
            <item>Quuz</item>
        ';

        $documentFragment = $document->createDocumentFragment();

        $documentFragment->appendXml($fragmentXml);

        $document->first('item:nth-child(2)')->replace($documentFragment);

        $expectedContent = ['Foo', 'Qux', 'Quux', 'Quuz', 'Baz'];

        foreach ($document->find('item') as $index => $childNode) {
            $this->assertEquals('item', $childNode->tag);
            $this->assertEquals($expectedContent[$index], $childNode->text());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testReplaceWithInvalidArgument()
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';

        $document = new Document($html, false);

        $document->find('li')[0]->replace(null);
    }

    /**
     * @expectedException LogicException
     */
    public function testReplaceElementWithoutParentNode()
    {
        $element = new Element('div', 'Foo');

        $element->replace(new Element('div', 'Bar'));
    }

    public function testGetLineNo()
    {
        $element = new Element('div');

        $this->assertEquals(0, $element->getLineNo());

        $html = '<ul>
            <li>One</li>
            <li>Two</li>
            <li>Three</li>
        </ul>';

        $document = new Document($html, false);

        $this->assertEquals(4, $document->find('li')[2]->getLineNo());
    }

    public function testCloneNode()
    {
        $element = new Element('input');

        $cloned = $element->cloneNode(true);

        $this->assertFalse($element->is($cloned));
    }

    public function testGetNode()
    {
        $node = $this->createDomElement('input');
        $element = new Element($node);

        $this->assertEquals($node, $element->getNode());
    }

    public function testGetDocument()
    {
        $html = $this->loadFixture('posts.html');

        $document = new Document($html, false);
        $element = $document->createElement('span', 'value');

        $this->assertEquals($document->getDocument(), $element->getDocument()->getDocument());
    }

    public function testToDocument()
    {
        $element = new Element('input');

        $document = $element->toDocument();

        $this->assertInstanceOf('DiDom\Document', $document);
        $this->assertEquals('UTF-8', $document->getDocument()->encoding);

        $document = $element->toDocument('CP1251');

        $this->assertEquals('CP1251', $document->getDocument()->encoding);
    }

    public function testSetMagicMethod()
    {
        $node = $this->createDomElement('input');

        $element = new Element($node);
        $element->name = 'username';

        $this->assertEquals('username', $element->getNode()->getAttribute('name'));
    }

    public function testGetMagicMethod()
    {
        $element = new Element('input', null, ['name' => 'username']);

        $this->assertEquals('username', $element->name);
    }

    public function testIssetMagicMethod()
    {
        $node = $this->createDomElement('input');
        $element = new Element($node);

        $this->assertFalse(isset($element->value));

        $node->setAttribute('value', 'test');
        $element = new Element($node);

        $this->assertTrue(isset($element->value));
    }

    public function testUnsetMagicMethod()
    {
        $element = new Element('input', null, ['name' => 'username']);

        $this->assertTrue($element->hasAttribute('name'));

        unset($element->name);

        $this->assertFalse($element->hasAttribute('name'));
    }

    public function testToString()
    {
        $element = new Element('span', 'hello');

        $this->assertEquals($element->html(), $element->__toString());
    }

    /**
     * @dataProvider findTests
     *
     * @param string $html
     * @param string $selector
     * @param string $type
     * @param int $count
     */
    public function testInvoke($html, $selector, $type, $count)
    {
        $document = new DOMDocument();
        $document->loadHTML($html);

        $node = $document->getElementsByTagName('body')->item(0);
        $element = new Element($node);

        $elements = $element($selector, $type);

        $this->assertTrue(is_array($elements));
        $this->assertEquals($count, count($elements));

        foreach ($elements as $element) {
            $this->assertInstanceOf('DiDom\Element', $element);
        }
    }
}

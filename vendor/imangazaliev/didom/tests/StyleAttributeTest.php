<?php

namespace DiDom\Tests;

use DiDom\Element;
use DiDom\StyleAttribute;
use InvalidArgumentException;

class StyleAttributeTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The element must contain DOMElement node
     */
    public function testConstructorWithTextNode()
    {
        $element = new Element(new \DOMText('foo'));

        new StyleAttribute($element);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The element must contain DOMElement node
     */
    public function testConstructorWithCommentNode()
    {
        $element = new Element(new \DOMComment('foo'));

        new StyleAttribute($element);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DiDom\StyleAttribute::setProperty expects parameter 1 to be string, NULL given
     */
    public function testSetPropertyWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->setProperty(null, '16px');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DiDom\StyleAttribute::setProperty expects parameter 2 to be string, NULL given
     */
    public function testSetPropertyWithInvalidPropertyValue()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->setProperty('font-size', null);
    }

    public function testSetProperty()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals('color: blue; border: 1px solid black', $element->getAttribute('style'));

        $styleAttribute->setProperty('font-size', '16px');

        $this->assertEquals('color: blue; border: 1px solid black; font-size: 16px', $element->getAttribute('style'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property name must be a string, integer given
     */
    public function testSetMultiplePropertiesWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->setMultipleProperties([
            'width' => '50px',
            'height',
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property value must be a string, NULL given
     */
    public function testSetMultiplePropertiesWithInvalidPropertyValue()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->setMultipleProperties([
            'width' => '50px',
            'height' => null,
        ]);
    }

    public function testSetMultipleProperties()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals('color: blue; border: 1px solid black', $element->getAttribute('style'));

        $styleAttribute->setMultipleProperties([
            'font-size' => '16px',
            'font-family' => 'Times',
        ]);

        $this->assertEquals('color: blue; border: 1px solid black; font-size: 16px; font-family: Times', $element->getAttribute('style'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DiDom\StyleAttribute::getProperty expects parameter 1 to be string, NULL given
     */
    public function testGetPropertyWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->getProperty(null);
    }

    /**
     * @param string $styleString
     * @param string $propertyName
     * @param string $expectedResult
     *
     * @dataProvider getPropertyDataProvider
     */
    public function testGetProperty($styleString, $propertyName, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($expectedResult, $styleAttribute->getProperty($propertyName));
    }

    public function getPropertyDataProvider()
    {
        return [
            [
                'color: blue; font-size: 16px; border: 1px solid black',
                'font-size',
                '16px',
            ],
            [
                'color: blue; font-size: 16px; border: 1px solid black;',
                'font-size',
                '16px',
            ],
            [
                'color: blue; font-size: 16px; border: 1px solid black;',
                'foo',
                null,
            ],
        ];
    }

    public function testGetPropertyWithDefaultValue()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertNull($styleAttribute->getProperty('font-size'));
        $this->assertEquals('16px', $styleAttribute->getProperty('font-size', '16px'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property name must be a string, NULL given
     */
    public function testGetMultiplePropertiesWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->getMultipleProperties(['color', null]);
    }

    /**
     * @param string $styleString
     * @param array $propertyNames
     * @param string $expectedResult
     *
     * @dataProvider getMultiplePropertiesDataProvider
     */
    public function testGetMultipleProperties($styleString, $propertyNames, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($expectedResult, $styleAttribute->getMultipleProperties($propertyNames));
    }

    public function getMultiplePropertiesDataProvider()
    {
        return [
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                ['font-size'],
                [
                    'font-size' => '16px',
                ],
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                ['font-size', 'border'],
                [
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                ['font-size', 'border', 'width'],
                [
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
        ];
    }

    /**
     * @param string $styleString
     * @param string $expectedResult
     *
     * @dataProvider getAllPropertiesDataProvider
     */
    public function testGetAllProperties($styleString, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($expectedResult, $styleAttribute->getAllProperties());
    }

    public function getAllPropertiesDataProvider()
    {
        return [
            [
                '',
                [],
            ],
            [
                'color: blue; font-size: 16px; border: 1px solid black',
                [
                    'color' => 'blue',
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
            [
                'color: blue; font-size: 16px; border: 1px solid black',
                [
                    'color' => 'blue',
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
        ];
    }

    public function testGetAllPropertiesAfterEmptyStyleAttribute()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals(['color' => 'blue'], $styleAttribute->getAllProperties());

        $element->setAttribute('style', '');

        $this->assertEquals([], $styleAttribute->getAllProperties());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DiDom\StyleAttribute::hasProperty expects parameter 1 to be string, NULL given
     */
    public function testHasPropertyWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->hasProperty(null);
    }

    public function testHasProperty()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertTrue($styleAttribute->hasProperty('color'));
        $this->assertFalse($styleAttribute->hasProperty('width'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DiDom\StyleAttribute::removeProperty expects parameter 1 to be string, NULL given
     */
    public function testRemovePropertyWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->removeProperty(null);
    }

    public function testRemoveProperty()
    {
        $styleString = 'color: blue; font-size: 16px; border: 1px solid black';

        $element = new Element('span', 'foo', [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($styleString, $element->getAttribute('style'));

        $styleAttribute->removeProperty('font-size');

        $this->assertEquals('color: blue; border: 1px solid black', $element->getAttribute('style'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property name must be a string, NULL given
     */
    public function testRemoveMultiplePropertiesWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->removeMultipleProperties(['color', null]);
    }

    /**
     * @param string $styleString
     * @param array $propertyNames
     * @param string $expectedResult
     *
     * @dataProvider removeMultiplePropertiesDataProvider
     */
    public function testRemoveMultipleProperties($styleString, $propertyNames, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($styleString, $element->getAttribute('style'));

        $styleAttribute->removeMultipleProperties($propertyNames);

        $this->assertEquals($expectedResult, $element->getAttribute('style'));
    }

    public function removeMultiplePropertiesDataProvider()
    {
        return [
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size',
                ],
                'color: blue; font-family: Times; border: 1px solid black',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border',
                ],
                'color: blue; font-family: Times',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border', 'width',
                ],
                'color: blue; font-family: Times',
            ],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property name must be a string, NULL given
     */
    public function testRemoveAllPropertiesWithInvalidPropertyName()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->removeAllProperties(['color', null]);
    }

    /**
     * @param string $styleString
     * @param array $exclusions
     * @param string $expectedResult
     *
     * @dataProvider removeAllPropertiesDataProvider
     */
    public function testRemoveAllProperties($styleString, $exclusions, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($styleString, $element->getAttribute('style'));

        $styleAttribute->removeAllProperties($exclusions);

        $this->assertEquals($expectedResult, $element->getAttribute('style'));
    }

    public function removeAllPropertiesDataProvider()
    {
        return [
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size',
                ],
                'font-size: 16px',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border',
                ],
                'font-size: 16px; border: 1px solid black',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border', 'width',
                ],
                'font-size: 16px; border: 1px solid black',
            ],
        ];
    }

    public function testGetElement()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; font-size: 16px',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertSame($element, $styleAttribute->getElement());
    }
}

<?php

namespace DiDom\Tests;

use DiDom\Query;
use InvalidArgumentException;
use RuntimeException;

class QueryTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DiDom\Query::compile expects parameter 1 to be string, NULL given
     */
    public function testCompileWithNonStringExpression()
    {
        Query::compile(null);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DiDom\Query::compile expects parameter 2 to be string, NULL given
     */
    public function testCompileWithNonStringExpressionType()
    {
        Query::compile('h1', null);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unknown expression type "foo"
     */
    public function testCompileWithUnknownExpressionType()
    {
        Query::compile('h1', 'foo');
    }

    /**
     * @dataProvider compileCssTests
     */
    public function testCompileCssSelector($selector, $xpath)
    {
        $this->assertEquals($xpath, Query::compile($selector));
    }

    /**
     * @dataProvider getSegmentsTests
     *
     * @param string $selector
     * @param array $segments
     */
    public function testGetSegments($selector, $segments)
    {
        $this->assertEquals($segments, Query::getSegments($selector));
    }

    /**
     * @dataProvider buildXpathTests
     *
     * @param array $segments
     * @param string $xpath
     */
    public function testBuildXpath($segments, $xpath)
    {
        $this->assertEquals($xpath, Query::buildXpath($segments));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBuildXpathWithEmptyArray()
    {
        Query::buildXpath([]);
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage The expression must not be empty
     */
    public function testCompileWithEmptyXpathExpression()
    {
        Query::compile('', Query::TYPE_XPATH);
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage The expression must not be empty
     */
    public function testCompileWithEmptyCssExpression()
    {
        Query::compile('', Query::TYPE_CSS);
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage The selector must not be empty
     */
    public function testGetSegmentsWithEmptySelector()
    {
        Query::getSegments('');
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage Invalid selector "input[=foo]": attribute name must not be empty
     */
    public function testEmptyAttributeName()
    {
        Query::compile('input[=foo]');
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage Unknown pseudo-class "unknown-pseudo-class"
     */
    public function testUnknownPseudoClass()
    {
        Query::compile('li:unknown-pseudo-class');
    }

    /**
     * @dataProvider containsInvalidCaseSensitiveParameterDataProvider
     */
    public function testContainsInvalidCaseSensitiveParameter($caseSensitive)
    {
        $message = sprintf('Parameter 2 of "contains" pseudo-class must be equal true or false, "%s" given', $caseSensitive);

        $this->setExpectedException('DiDom\Exceptions\InvalidSelectorException', $message);

        Query::compile("a:contains('Log in', {$caseSensitive})");
    }

    public function containsInvalidCaseSensitiveParameterDataProvider()
    {
        return [
            ['foo'],
            ['TRUE'],
            ['FALSE'],
        ];
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage nth-child (or nth-last-child) expression must not be empty
     */
    public function testEmptyNthExpression()
    {
        Query::compile('li:nth-child()');
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage Invalid property "::"
     */
    public function testEmptyProperty()
    {
        Query::compile('li::');
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage Unknown property "foo"
     */
    public function testInvalidProperty()
    {
        Query::compile('li::foo');
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage Invalid nth-child expression "foo"
     */
    public function testUnknownNthExpression()
    {
        Query::compile('li:nth-child(foo)');
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage Invalid selector "."
     */
    public function testGetSegmentsWithEmptyClassName()
    {
        Query::getSegments('.');
    }

    /**
     * @expectedException \DiDom\Exceptions\InvalidSelectorException
     * @expectedExceptionMessage Invalid selector "."
     */
    public function testCompilehWithEmptyClassName()
    {
        Query::compile('span.');
    }

    public function testCompileXpath()
    {
        $this->assertEquals('//div', Query::compile('//div', Query::TYPE_XPATH));
    }

    public function testSetCompiledInvalidArgumentType()
    {
        if (PHP_VERSION_ID >= 70000) {
            $this->setExpectedException('TypeError');
        } else {
            $this->setExpectedException('PHPUnit_Framework_Error');
        }

        Query::setCompiled(null);
    }

    public function testSetCompiled()
    {
        $xpath = "//*[@id='foo']//*[contains(concat(' ', normalize-space(@class), ' '), ' bar ')]//baz";
        $compiled = ['#foo .bar baz' => $xpath];

        Query::setCompiled($compiled);

        $this->assertEquals($compiled, Query::getCompiled());
    }

    public function testGetCompiled()
    {
        Query::setCompiled([]);

        $selector = '#foo .bar baz';
        $xpath = '//*[@id="foo"]//*[contains(concat(" ", normalize-space(@class), " "), " bar ")]//baz';
        $compiled = [$selector => $xpath];

        Query::compile($selector);

        $this->assertEquals($compiled, Query::getCompiled());
    }

    public function compileCssTests()
    {
        $compiled = [
            ['a', '//a'],
            ['foo bar baz', '//foo//bar//baz'],
            ['foo > bar > baz', '//foo/bar/baz'],
            ['#foo', '//*[@id="foo"]'],
            ['.bar', '//*[contains(concat(" ", normalize-space(@class), " "), " bar ")]'],
            ['*[foo=bar]', '//*[@foo="bar"]'],
            ['*[foo="bar"]', '//*[@foo="bar"]'],
            ['*[foo=\'bar\']', '//*[@foo="bar"]'],
            ['select[name=category] option[selected=selected]', '//select[@name="category"]//option[@selected="selected"]'],
            ['*[^data-]', '//*[@*[starts-with(name(), "data-")]]'],
            ['*[^data-=foo]', '//*[@*[starts-with(name(), "data-")]="foo"]'],
            ['a[href^=https]', '//a[starts-with(@href, "https")]'],
            ['img[src$=png]', '//img[substring(@src, string-length(@src) - string-length("png") + 1) = "png"]'],
            ['a[href*=example.com]', '//a[contains(@href, "example.com")]'],
            ['script[!src]', '//script[not(@src)]'],
            ['a[href!="http://foo.com/"]', '//a[not(@href="http://foo.com/")]'],
            ['a[foo~="bar"]', '//a[contains(concat(" ", normalize-space(@foo), " "), " bar ")]'],
            ['input, textarea, select', '//input|//textarea|//select'],
            ['input[name="name"], textarea[name="description"], select[name="type"]', '//input[@name="name"]|//textarea[@name="description"]|//select[@name="type"]'],
            ['li:first-child', '//li[position() = 1]'],
            ['li:last-child', '//li[position() = last()]'],
            ['*:not(a[href*="example.com"])', '//*[not(self::a[contains(@href, "example.com")])]'],
            ['ul:empty', '//ul[count(descendant::*) = 0]'],
            ['ul:not-empty', '//ul[count(descendant::*) > 0]'],
            ['li:nth-child(odd)', '//*[(name()="li") and (position() mod 2 = 1 and position() >= 1)]'],
            ['li:nth-child(even)', '//*[(name()="li") and (position() mod 2 = 0 and position() >= 0)]'],
            ['li:nth-child(3)', '//*[(name()="li") and (position() = 3)]'],
            ['li:nth-child(-3)', '//*[(name()="li") and (position() = -3)]'],
            ['li:nth-child(3n)', '//*[(name()="li") and ((position() + 0) mod 3 = 0 and position() >= 0)]'],
            ['li:nth-child(3n+1)', '//*[(name()="li") and ((position() - 1) mod 3 = 0 and position() >= 1)]'],
            ['li:nth-child(3n-1)', '//*[(name()="li") and ((position() + 1) mod 3 = 0 and position() >= 1)]'],
            ['li:nth-child(n+3)', '//*[(name()="li") and ((position() - 3) mod 1 = 0 and position() >= 3)]'],
            ['li:nth-child(n-3)', '//*[(name()="li") and ((position() + 3) mod 1 = 0 and position() >= 3)]'],
            ['li:nth-of-type(odd)', '//li[position() mod 2 = 1 and position() >= 1]'],
            ['li:nth-of-type(even)', '//li[position() mod 2 = 0 and position() >= 0]'],
            ['li:nth-of-type(3)', '//li[position() = 3]'],
            ['li:nth-of-type(-3)', '//li[position() = -3]'],
            ['li:nth-of-type(3n)', '//li[(position() + 0) mod 3 = 0 and position() >= 0]'],
            ['li:nth-of-type(3n+1)', '//li[(position() - 1) mod 3 = 0 and position() >= 1]'],
            ['li:nth-of-type(3n-1)', '//li[(position() + 1) mod 3 = 0 and position() >= 1]'],
            ['li:nth-of-type(n+3)', '//li[(position() - 3) mod 1 = 0 and position() >= 3]'],
            ['li:nth-of-type(n-3)', '//li[(position() + 3) mod 1 = 0 and position() >= 3]'],
            ['ul:has(li.item)', '//ul[.//li[contains(concat(" ", normalize-space(@class), " "), " item ")]]'],
            ['form[name=register]:has(input[name=foo])', '//form[(@name="register") and (.//input[@name="foo"])]'],
            ['ul li a::text', '//ul//li//a/text()'],
            ['ul li a::text()', '//ul//li//a/text()'],
            ['ul li a::attr(href)', '//ul//li//a/@*[name() = "href"]'],
            ['ul li a::attr(href, title)', '//ul//li//a/@*[name() = "href" or name() = "title"]'],
            ['> ul li a', '/ul//li//a'],
        ];

        $compiled = array_merge($compiled, $this->getContainsPseudoClassTests());
        $compiled = array_merge($compiled, $this->getPropertiesTests());

        $compiled = array_merge($compiled, [
            ['a[title="foo, bar::baz"]', '//a[@title="foo, bar::baz"]'],
        ]);

        return $compiled;
    }

    private function getContainsPseudoClassTests()
    {
        $strToLowerFunction = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

        $containsXpath = [
            // caseSensitive = true, fullMatch = false
            ['li:contains(foo)', '//li[contains(text(), "foo")]'],
            ['li:contains("foo")', '//li[contains(text(), "foo")]'],
            ['li:contains(\'foo\')', '//li[contains(text(), "foo")]'],

            // caseSensitive = true, fullMatch = false
            ['li:contains(foo, true)', '//li[contains(text(), "foo")]'],
            ['li:contains("foo", true)', '//li[contains(text(), "foo")]'],
            ['li:contains(\'foo\', true)', '//li[contains(text(), "foo")]'],

            // caseSensitive = true, fullMatch = false
            ['li:contains(foo, true, false)', '//li[contains(text(), "foo")]'],
            ['li:contains("foo", true, false)', '//li[contains(text(), "foo")]'],
            ['li:contains(\'foo\', true, false)', '//li[contains(text(), "foo")]'],

            // caseSensitive = true, fullMatch = true
            ['li:contains(foo, true, true)', '//li[text() = "foo"]'],
            ['li:contains("foo", true, true)', '//li[text() = "foo"]'],
            ['li:contains(\'foo\', true, true)', '//li[text() = "foo"]'],

            // caseSensitive = false, fullMatch = false
            ['li:contains(foo, false)', "//li[contains(php:functionString(\"{$strToLowerFunction}\", .), php:functionString(\"{$strToLowerFunction}\", \"foo\"))]"],
            ['li:contains("foo", false)', "//li[contains(php:functionString(\"{$strToLowerFunction}\", .), php:functionString(\"{$strToLowerFunction}\", \"foo\"))]"],
            ['li:contains(\'foo\', false)', "//li[contains(php:functionString(\"{$strToLowerFunction}\", .), php:functionString(\"{$strToLowerFunction}\", \"foo\"))]"],

            // caseSensitive = false, fullMatch = false
            ['li:contains(foo, false, false)', "//li[contains(php:functionString(\"{$strToLowerFunction}\", .), php:functionString(\"{$strToLowerFunction}\", \"foo\"))]"],
            ['li:contains("foo", false, false)', "//li[contains(php:functionString(\"{$strToLowerFunction}\", .), php:functionString(\"{$strToLowerFunction}\", \"foo\"))]"],
            ['li:contains(\'foo\', false, false)', "//li[contains(php:functionString(\"{$strToLowerFunction}\", .), php:functionString(\"{$strToLowerFunction}\", \"foo\"))]"],

            // caseSensitive = false, fullMatch = true
            ['li:contains(foo, false, true)', "//li[php:functionString(\"{$strToLowerFunction}\", .) = php:functionString(\"{$strToLowerFunction}\", \"foo\")]"],
            ['li:contains("foo", false, true)', "//li[php:functionString(\"{$strToLowerFunction}\", .) = php:functionString(\"{$strToLowerFunction}\", \"foo\")]"],
            ['li:contains(\'foo\', false, true)', "//li[php:functionString(\"{$strToLowerFunction}\", .) = php:functionString(\"{$strToLowerFunction}\", \"foo\")]"],
        ];

        return $containsXpath;
    }

    private function getPropertiesTests()
    {
        return [
            ['a::text', '//a/text()'],
            ['a::text()', '//a/text()'],
            ['a::attr', '//a/@*'],
            ['a::attr()', '//a/@*'],
            ['a::attr(href)', '//a/@*[name() = "href"]'],
            ['a::attr(href,title)', '//a/@*[name() = "href" or name() = "title"]'],
            ['a::attr(href, title)', '//a/@*[name() = "href" or name() = "title"]'],
        ];
    }

    public function buildXpathTests()
    {
        $xpath = [
            '//a',
            '//*[@id="foo"]',
            '//a[@id="foo"]',
            '//a[contains(concat(" ", normalize-space(@class), " "), " foo ")]',
            '//a[(contains(concat(" ", normalize-space(@class), " "), " foo ")) and (contains(concat(" ", normalize-space(@class), " "), " bar "))]',
            '//a[@href]',
            '//a[@href="http://example.com/"]',
            '//a[(@href="http://example.com/") and (@title="Example Domain")]',
            '//a[(@target="_blank") and (starts-with(@href, "https"))]',
            '//a[substring(@href, string-length(@href) - string-length(".com") + 1) = ".com"]',
            '//a[contains(@href, "example")]',
            '//a[not(@href="http://foo.com/")]',
            '//script[not(@src)]',
            '//li[position() = 1]',
            '//*[(@id="id") and (contains(concat(" ", normalize-space(@class), " "), " foo ")) and (@name="value") and (position() = 1)]',
        ];

        $segments = [
            ['tag' => 'a'],
            ['id' => 'foo'],
            ['tag' => 'a', 'id' => 'foo'],
            ['tag' => 'a', 'classes' => ['foo']],
            ['tag' => 'a', 'classes' => ['foo', 'bar']],
            ['tag' => 'a', 'attributes' => ['href' => null]],
            ['tag' => 'a', 'attributes' => ['href' => 'http://example.com/']],
            ['tag' => 'a', 'attributes' => ['href' => 'http://example.com/', 'title' => 'Example Domain']],
            ['tag' => 'a', 'attributes' => ['target' => '_blank', 'href^' => 'https']],
            ['tag' => 'a', 'attributes' => ['href$' => '.com']],
            ['tag' => 'a', 'attributes' => ['href*' => 'example']],
            ['tag' => 'a', 'attributes' => ['href!' => 'http://foo.com/']],
            ['tag' => 'script', 'attributes' => ['!src' => null]],
            ['tag' => 'li', 'pseudo' => 'first-child'],
            ['tag' => '*', 'id' => 'id', 'classes' => ['foo'], 'attributes' => ['name' => 'value'], 'pseudo' => 'first-child', 'rel' => '>'],
        ];

        $parameters = [];

        foreach ($segments as $index => $segment) {
            $parameters[] = [$segment, $xpath[$index]];
        }

        return $parameters;
    }

    public function getSegmentsTests()
    {
        $segments = [
            ['selector' => 'a', 'tag' => 'a'],
            ['selector' => '#foo', 'id' => 'foo'],
            ['selector' => 'a#foo', 'tag' => 'a', 'id' => 'foo'],
            ['selector' => 'a.foo', 'tag' => 'a', 'classes' => ['foo']],
            ['selector' => 'a.foo.bar', 'tag' => 'a', 'classes' => ['foo', 'bar']],
            ['selector' => 'a[href]', 'tag' => 'a', 'attributes' => ['href' => null]],
            ['selector' => 'a[href=http://example.com/]', 'tag' => 'a', 'attributes' => ['href' => 'http://example.com/']],
            ['selector' => 'a[href="http://example.com/"]', 'tag' => 'a', 'attributes' => ['href' => 'http://example.com/']],
            ['selector' => 'a[href=\'http://example.com/\']', 'tag' => 'a', 'attributes' => ['href' => 'http://example.com/']],
            ['selector' => 'a[href=http://example.com/][title=Example Domain]', 'tag' => 'a', 'attributes' => ['href' => 'http://example.com/', 'title' => 'Example Domain']],
            ['selector' => 'a[href=http://example.com/][href=http://example.com/404]', 'tag' => 'a', 'attributes' => ['href' => 'http://example.com/404']],
            ['selector' => 'a[href^=https]', 'tag' => 'a', 'attributes' => ['href^' => 'https']],
            ['selector' => 'li:first-child', 'tag' => 'li', 'pseudo' => 'first-child'],
            ['selector' => 'ul >', 'tag' => 'ul', 'rel' => '>'],
            ['selector' => '#id.foo[name=value]:first-child >', 'id' => 'id', 'classes' => ['foo'], 'attributes' => ['name' => 'value'], 'pseudo' => 'first-child', 'rel' => '>'],
            ['selector' => 'li.bar:nth-child(2n)', 'tag' => 'li', 'classes' => ['bar'], 'pseudo' => 'nth-child', 'expr' => '2n'],
        ];

        $parameters = [];

        foreach ($segments as $segment) {
            $parameters[] = [$segment['selector'], $segment];
        }

        return $parameters;
    }
}

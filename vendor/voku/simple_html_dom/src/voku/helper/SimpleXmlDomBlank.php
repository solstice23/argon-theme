<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * @noinspection PhpHierarchyChecksInspection
 *
 * {@inheritdoc}
 *
 * @implements \IteratorAggregate<int, \DOMNode>
 */
class SimpleXmlDomBlank extends AbstractSimpleXmlDom implements \IteratorAggregate, SimpleXmlDomInterface
{
    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     *
     * @return SimpleXmlDomInterface|string|null
     */
    public function __call($name, $arguments)
    {
        $name = \strtolower($name);

        if (isset(self::$functionAliases[$name])) {
            return \call_user_func_array([$this, self::$functionAliases[$name]], $arguments);
        }

        throw new \BadMethodCallException('Method does not exist');
    }

    /**
     * Find list of nodes with a CSS selector.
     *
     * @param string   $selector
     * @param int|null $idx
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function find(string $selector, $idx = null)
    {
        return new SimpleXmlDomNodeBlank();
    }

    /**
     * Returns an array of attributes.
     *
     * @return null
     */
    public function getAllAttributes()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function hasAttributes(): bool
    {
        return false;
    }

    /**
     * Return attribute value.
     *
     * @param string $name
     *
     * @return string
     */
    public function getAttribute(string $name): string
    {
        return '';
    }

    /**
     * Determine if an attribute exists on the element.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return false;
    }

    /**
     * Get dom node's inner xml.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function innerXml(bool $multiDecodeNewHtmlEntity = false): string
    {
        return '';
    }

    /**
     * Remove attribute.
     *
     * @param string $name <p>The name of the html-attribute.</p>
     *
     * @return SimpleXmlDomInterface
     */
    public function removeAttribute(string $name): SimpleXmlDomInterface
    {
        return $this;
    }

    /**
     * @param string $string
     *
     * @return SimpleXmlDomInterface
     */
    protected function replaceChildWithString(string $string): SimpleXmlDomInterface
    {
        return new static();
    }

    /**
     * @param string $string
     *
     * @return SimpleXmlDomInterface
     */
    protected function replaceNodeWithString(string $string): SimpleXmlDomInterface
    {
        return new static();
    }

    /**
     * @param string $string
     *
     * @return SimpleXmlDomInterface
     */
    protected function replaceTextWithString($string): SimpleXmlDomInterface
    {
        return new static();
    }

    /**
     * Set attribute value.
     *
     * @param string      $name       <p>The name of the html-attribute.</p>
     * @param string|null $value      <p>Set to NULL or empty string, to remove the attribute.</p>
     * @param bool        $strict     </p>
     *                                $value must be NULL, to remove the attribute,
     *                                so that you can set an empty string as attribute-value e.g. autofocus=""
     *                                </p>
     *
     * @return SimpleXmlDomInterface
     */
    public function setAttribute(string $name, $value = null, bool $strict = false): SimpleXmlDomInterface
    {
        return $this;
    }

    /**
     * Get dom node's plain text.
     *
     * @return string
     */
    public function text(): string
    {
        return '';
    }

    /**
     * Get dom node's outer html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function xml(bool $multiDecodeNewHtmlEntity = false): string
    {
        return '';
    }

    /**
     * Returns children of node.
     *
     * @param int $idx
     *
     * @return null
     */
    public function childNodes(int $idx = -1)
    {
        return null;
    }

    /**
     * Find nodes with a CSS selector.
     *
     * @param string $selector
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function findMulti(string $selector): SimpleXmlDomNodeInterface
    {
        return new SimpleXmlDomNodeBlank();
    }

    /**
     * Find nodes with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false
     */
    public function findMultiOrFalse(string $selector)
    {
        return false;
    }

    /**
     * Find one node with a CSS selector.
     *
     * @param string $selector
     *
     * @return SimpleXmlDomInterface
     */
    public function findOne(string $selector): SimpleXmlDomInterface
    {
        return new static();
    }

    /**
     * Find one node with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false
     */
    public function findOneOrFalse(string $selector)
    {
        return false;
    }

    /**
     * Returns the first child of node.
     *
     * @return null
     */
    public function firstChild()
    {
        return null;
    }

    /**
     * Return elements by ".class".
     *
     * @param string $class
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function getElementByClass(string $class): SimpleXmlDomNodeInterface
    {
        return new SimpleXmlDomNodeBlank();
    }

    /**
     * Return element by #id.
     *
     * @param string $id
     *
     * @return SimpleXmlDomInterface
     */
    public function getElementById(string $id): SimpleXmlDomInterface
    {
        return new static();
    }

    /**
     * Return element by tag name.
     *
     * @param string $name
     *
     * @return SimpleXmlDomInterface
     */
    public function getElementByTagName(string $name): SimpleXmlDomInterface
    {
        return new static();
    }

    /**
     * Returns elements by "#id".
     *
     * @param string   $id
     * @param int|null $idx
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function getElementsById(string $id, $idx = null)
    {
        return new SimpleXmlDomNodeBlank();
    }

    /**
     * Returns elements by tag name.
     *
     * @param string   $name
     * @param int|null $idx
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function getElementsByTagName(string $name, $idx = null)
    {
        return new SimpleXmlDomNodeBlank();
    }

    /**
     * @return \DOMNode
     */
    public function getNode(): \DOMNode
    {
        return new \DOMNode();
    }

    /**
     * Create a new "XmlDomParser"-object from the current context.
     *
     * @return XmlDomParser
     */
    public function getXmlDomParser(): XmlDomParser
    {
        return new XmlDomParser($this);
    }

    /**
     * Get dom node's inner html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function innerHtml(bool $multiDecodeNewHtmlEntity = false): string
    {
        return '';
    }

    /**
     * Nodes can get partially destroyed in which they're still an
     * actual DOM node (such as \DOMElement) but almost their entire
     * body is gone, including the `nodeType` attribute.
     *
     * @return bool true if node has been destroyed
     */
    public function isRemoved(): bool
    {
        return true;
    }

    /**
     * Returns the last child of node.
     *
     * @return null
     */
    public function lastChild()
    {
        return null;
    }

    /**
     * Returns the next sibling of node.
     *
     * @return null
     */
    public function nextSibling()
    {
        return null;
    }

    /**
     * Returns the next sibling of node.
     *
     * @return null
     */
    public function nextNonWhitespaceSibling()
    {
        return null;
    }

    /**
     * Returns the parent of node.
     *
     * @return SimpleXmlDomInterface
     */
    public function parentNode(): SimpleXmlDomInterface
    {
        return new static();
    }

    /**
     * Returns the previous sibling of node.
     *
     * @return null
     */
    public function previousSibling()
    {
        return null;
    }

    /**
     * @param string|string[]|null $value <p>
     *                                    null === get the current input value
     *                                    text === set a new input value
     *                                    </p>
     *
     * @return string|string[]|null
     */
    public function val($value = null)
    {
        return null;
    }

    /**
     * Retrieve an external iterator.
     *
     * @see  http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     *                           <p>
     *                              An instance of an object implementing <b>Iterator</b> or
     *                              <b>Traversable</b>
     *                           </p>
     */
    public function getIterator(): SimpleXmlDomNodeInterface
    {
        return new SimpleXmlDomNodeBlank();
    }
}

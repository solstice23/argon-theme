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
class SimpleHtmlDomBlank extends AbstractSimpleHtmlDom implements \IteratorAggregate, SimpleHtmlDomInterface
{
    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     *
     * @return SimpleHtmlDomInterface|string|null
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
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function find(string $selector, $idx = null)
    {
        return new SimpleHtmlDomNodeBlank();
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
     * Get dom node's outer html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function html(bool $multiDecodeNewHtmlEntity = false): string
    {
        return '';
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
     * Remove attribute.
     *
     * @param string $name <p>The name of the html-attribute.</p>
     *
     * @return SimpleHtmlDomInterface
     */
    public function removeAttribute(string $name): SimpleHtmlDomInterface
    {
        return $this;
    }

    /**
     * @param string $string
     *
     * @return SimpleHtmlDomInterface
     */
    protected function replaceChildWithString(string $string): SimpleHtmlDomInterface
    {
        return new static();
    }

    /**
     * @param string $string
     *
     * @return SimpleHtmlDomInterface
     */
    protected function replaceNodeWithString(string $string): SimpleHtmlDomInterface
    {
        return new static();
    }

    /**
     * @param string $string
     *
     * @return SimpleHtmlDomInterface
     */
    protected function replaceTextWithString($string): SimpleHtmlDomInterface
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
     * @return SimpleHtmlDomInterface
     */
    public function setAttribute(string $name, $value = null, bool $strict = false): SimpleHtmlDomInterface
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
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function findMulti(string $selector): SimpleHtmlDomNodeInterface
    {
        return new SimpleHtmlDomNodeBlank();
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
     * @return SimpleHtmlDomInterface
     */
    public function findOne(string $selector): SimpleHtmlDomInterface
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
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementByClass(string $class): SimpleHtmlDomNodeInterface
    {
        return new SimpleHtmlDomNodeBlank();
    }

    /**
     * Return element by #id.
     *
     * @param string $id
     *
     * @return SimpleHtmlDomInterface
     */
    public function getElementById(string $id): SimpleHtmlDomInterface
    {
        return new static();
    }

    /**
     * Return element by tag name.
     *
     * @param string $name
     *
     * @return SimpleHtmlDomInterface
     */
    public function getElementByTagName(string $name): SimpleHtmlDomInterface
    {
        return new static();
    }

    /**
     * Returns elements by "#id".
     *
     * @param string   $id
     * @param int|null $idx
     *
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementsById(string $id, $idx = null)
    {
        return new SimpleHtmlDomNodeBlank();
    }

    /**
     * Returns elements by tag name.
     *
     * @param string   $name
     * @param int|null $idx
     *
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementsByTagName(string $name, $idx = null)
    {
        return new SimpleHtmlDomNodeBlank();
    }

    /**
     * Create a new "HtmlDomParser"-object from the current context.
     *
     * @return HtmlDomParser
     */
    public function getHtmlDomParser(): HtmlDomParser
    {
        return new HtmlDomParser($this);
    }

    /**
     * @return \DOMNode
     */
    public function getNode(): \DOMNode
    {
        return new \DOMNode();
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
     * @return SimpleHtmlDomInterface
     */
    public function parentNode(): SimpleHtmlDomInterface
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
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     *                           <p>
     *                              An instance of an object implementing <b>Iterator</b> or
     *                              <b>Traversable</b>
     *                           </p>
     */
    public function getIterator(): SimpleHtmlDomNodeInterface
    {
        return new SimpleHtmlDomNodeBlank();
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
}

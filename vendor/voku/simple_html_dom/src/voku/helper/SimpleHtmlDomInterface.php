<?php

namespace voku\helper;

/**
 * @property string      $outertext
 *                            <p>Get dom node's outer html (alias for "outerHtml").</p>
 * @property string      $outerhtml
 *                            <p>Get dom node's outer html.</p>
 * @property string      $innertext
 *                            <p>Get dom node's inner html (alias for "innerHtml").</p>
 * @property string      $innerhtml
 *                            <p>Get dom node's inner html.</p>
 * @property string      $plaintext
 *                            <p>Get dom node's plain text.</p>
 * @property string      $class
 *                            <p>Get dom node's class attribute.</p>
 * @property string      $id
 *                            <p>Get dom node's id attribute.</p>
 * @property SimpleHtmlAttributes $classList
 *                            <p>Get dom node attributes.</p>
 * @property-read string $tag
 *                            <p>Get dom node name.</p>
 * @property-read string $attr
 *                            <p>Get dom node attributes.</p>
 * @property-read string $text
 *                            <p>Get dom node name.</p>
 * @property-read string $html
 *                            <p>Get dom node's outer html.</p>
 *
 * @method SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface|null children() children($idx = -1)
 *                                           <p>Returns children of node.</p>
 * @method SimpleHtmlDomInterface|null first_child()
 *                                           <p>Returns the first child of node.</p>
 * @method SimpleHtmlDomInterface|null last_child()
 *                                           <p>Returns the last child of node.</p>
 * @method SimpleHtmlDomInterface|null next_sibling()
 *                                           <p>Returns the next sibling of node.</p>
 * @method SimpleHtmlDomInterface|null prev_sibling()
 *                                           <p>Returns the previous sibling of node.</p>
 * @method SimpleHtmlDomInterface|null parent()
 *                                           <p>Returns the parent of node.</p>
 * @method string outerText()
 *                                           <p>Get dom node's outer html (alias for "outerHtml()").</p>
 * @method string outerHtml()
 *                                           <p>Get dom node's outer html.</p>
 * @method string innerText()
 *                                           <p>Get dom node's inner html (alias for "innerHtml()").</p>
 *
 * @extends \IteratorAggregate<int, \DOMNode>
 */
interface SimpleHtmlDomInterface extends \IteratorAggregate
{
    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     *
     * @return SimpleHtmlDomInterface|string|null
     */
    public function __call($name, $arguments);

    /**
     * @param string $name
     *
     * @return array|string|null
     */
    public function __get($name);

    /**
     * @param string $selector
     * @param int    $idx
     *
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function __invoke($selector, $idx = null);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name);

    /**
     * @return string
     */
    public function __toString();

    /**
     * Returns children of node.
     *
     * @param int $idx
     *
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface|null
     */
    public function childNodes(int $idx = -1);

    /**
     * Find list of nodes with a CSS selector.
     *
     * @param string   $selector
     * @param int|null $idx
     *
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function find(string $selector, $idx = null);

    /**
     * Find nodes with a CSS selector.
     *
     * @param string $selector
     *
     * @return SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function findMulti(string $selector): SimpleHtmlDomNodeInterface;

    /**
     * Find nodes with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function findMultiOrFalse(string $selector);

    /**
     * Find one node with a CSS selector.
     *
     * @param string $selector
     *
     * @return SimpleHtmlDomInterface
     */
    public function findOne(string $selector): self;

    /**
     * Find one node with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false|SimpleHtmlDomInterface
     */
    public function findOneOrFalse(string $selector);

    /**
     * Returns the first child of node.
     *
     * @return SimpleHtmlDomInterface|null
     */
    public function firstChild();

    /**
     * Returns an array of attributes.
     *
     * @return string[]|null
     */
    public function getAllAttributes();

    /**
     * Return attribute value.
     *
     * @param string $name
     *
     * @return string
     */
    public function getAttribute(string $name): string;

    /**
     * Return elements by ".class".
     *
     * @param string $class
     *
     * @return SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementByClass(string $class);

    /**
     * Return element by "#id".
     *
     * @param string $id
     *
     * @return SimpleHtmlDomInterface
     */
    public function getElementById(string $id): self;

    /**
     * Return element by tag name.
     *
     * @param string $name
     *
     * @return SimpleHtmlDomInterface
     */
    public function getElementByTagName(string $name): self;

    /**
     * Returns elements by "#id".
     *
     * @param string   $id
     * @param int|null $idx
     *
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementsById(string $id, $idx = null);

    /**
     * Returns elements by tag name.
     *
     * @param string   $name
     * @param int|null $idx
     *
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementsByTagName(string $name, $idx = null);

    /**
     * Create a new "HtmlDomParser"-object from the current context.
     *
     * @return HtmlDomParser
     */
    public function getHtmlDomParser(): HtmlDomParser;

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
    public function getIterator(): SimpleHtmlDomNodeInterface;

    /**
     * @return \DOMNode
     */
    public function getNode(): \DOMNode;

    /**
     * Determine if an attribute exists on the element.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool;

    /**
     * Get dom node's outer html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function html(bool $multiDecodeNewHtmlEntity = false): string;

    /**
     * Get dom node's inner html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function innerHtml(bool $multiDecodeNewHtmlEntity = false): string;

    /**
     * Get dom node's inner html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function innerXml(bool $multiDecodeNewHtmlEntity = false): string;

    /**
     * Nodes can get partially destroyed in which they're still an
     * actual DOM node (such as \DOMElement) but almost their entire
     * body is gone, including the `nodeType` attribute.
     *
     * @return bool true if node has been destroyed
     */
    public function isRemoved(): bool;

    /**
     * Returns the last child of node.
     *
     * @return SimpleHtmlDomInterface|null
     */
    public function lastChild();

    /**
     * Returns the next sibling of node.
     *
     * @return SimpleHtmlDomInterface|null
     */
    public function nextSibling();

    /**
     * Returns the next sibling of node and it will ignore whitespace elements.
     *
     * @return SimpleHtmlDomInterface|null
     */
    public function nextNonWhitespaceSibling();

    /**
     * Returns the parent of node.
     *
     * @return SimpleHtmlDomInterface
     */
    public function parentNode(): self;

    /**
     * Returns the previous sibling of node.
     *
     * @return SimpleHtmlDomInterface|null
     */
    public function previousSibling();

    /**
     * Remove attribute.
     *
     * @param string $name <p>The name of the html-attribute.</p>
     *
     * @return SimpleHtmlDomInterface
     */
    public function removeAttribute(string $name): self;

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
    public function setAttribute(string $name, $value = null, bool $strict = false): self;

    /**
     * Get dom node's plain text.
     *
     * @return string
     */
    public function text(): string;

    /**
     * @param string|string[]|null $value <p>
     *                                    null === get the current input value
     *                                    text === set a new input value
     *                                    </p>
     *
     * @return string|string[]|null
     */
    public function val($value = null);
}

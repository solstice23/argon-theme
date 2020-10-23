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
class SimpleXmlDom extends AbstractSimpleXmlDom implements \IteratorAggregate, SimpleXmlDomInterface
{
    /**
     * @param \DOMElement|\DOMNode $node
     */
    public function __construct(\DOMNode $node)
    {
        $this->node = $node;
    }

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
     * @return SimpleXmlDomInterface|SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function find(string $selector, $idx = null)
    {
        return $this->getXmlDomParser()->find($selector, $idx);
    }

    /**
     * Returns an array of attributes.
     *
     * @return string[]|null
     */
    public function getAllAttributes()
    {
        if (
            $this->node
            &&
            $this->node->hasAttributes()
        ) {
            $attributes = [];
            foreach ($this->node->attributes ?? [] as $attr) {
                $attributes[$attr->name] = XmlDomParser::putReplacedBackToPreserveHtmlEntities($attr->value);
            }

            return $attributes;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasAttributes(): bool
    {
        return $this->node->hasAttributes();
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
        if ($this->node instanceof \DOMElement) {
            return XmlDomParser::putReplacedBackToPreserveHtmlEntities(
                $this->node->getAttribute($name)
            );
        }

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
        if (!$this->node instanceof \DOMElement) {
            return false;
        }

        return $this->node->hasAttribute($name);
    }

    /**
     * Get dom node's inner html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function innerXml(bool $multiDecodeNewHtmlEntity = false): string
    {
        return $this->getXmlDomParser()->innerXml($multiDecodeNewHtmlEntity);
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
        if (\method_exists($this->node, 'removeAttribute')) {
            $this->node->removeAttribute($name);
        }

        return $this;
    }

    /**
     * Replace child node.
     *
     * @param string $string
     *
     * @return SimpleXmlDomInterface
     */
    protected function replaceChildWithString(string $string): SimpleXmlDomInterface
    {
        if (!empty($string)) {
            $newDocument = new XmlDomParser($string);

            $tmpDomString = $this->normalizeStringForComparision($newDocument);
            $tmpStr = $this->normalizeStringForComparision($string);
            if ($tmpDomString !== $tmpStr) {
                throw new \RuntimeException(
                    'Not valid XML fragment!' . "\n" .
                    $tmpDomString . "\n" .
                    $tmpStr
                );
            }
        }

        /** @var \DOMNode[] $remove_nodes */
        $remove_nodes = [];
        if ($this->node->childNodes->length > 0) {
            // INFO: We need to fetch the nodes first, before we can delete them, because of missing references in the dom,
            // if we delete the elements on the fly.
            foreach ($this->node->childNodes as $node) {
                $remove_nodes[] = $node;
            }
        }
        foreach ($remove_nodes as $remove_node) {
            $this->node->removeChild($remove_node);
        }

        if (!empty($newDocument)) {
            $ownerDocument = $this->node->ownerDocument;
            if (
                $ownerDocument
                &&
                $newDocument->getDocument()->documentElement
            ) {
                $newNode = $ownerDocument->importNode($newDocument->getDocument()->documentElement, true);
                /** @noinspection UnusedFunctionResultInspection */
                $this->node->appendChild($newNode);
            }
        }

        return $this;
    }

    /**
     * Replace this node.
     *
     * @param string $string
     *
     * @return SimpleXmlDomInterface
     */
    protected function replaceNodeWithString(string $string): SimpleXmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }

            return $this;
        }

        $newDocument = new XmlDomParser($string);

        $tmpDomOuterTextString = $this->normalizeStringForComparision($newDocument);
        $tmpStr = $this->normalizeStringForComparision($string);
        if ($tmpDomOuterTextString !== $tmpStr) {
            throw new \RuntimeException(
                'Not valid XML fragment!' . "\n"
                . $tmpDomOuterTextString . "\n" .
                $tmpStr
            );
        }

        $ownerDocument = $this->node->ownerDocument;
        if (
            $ownerDocument === null
            ||
            $newDocument->getDocument()->documentElement === null
        ) {
            return $this;
        }

        $newNode = $ownerDocument->importNode($newDocument->getDocument()->documentElement, true);

        $this->node->parentNode->replaceChild($newNode, $this->node);
        $this->node = $newNode;

        return $this;
    }

    /**
     * Replace this node with text
     *
     * @param string $string
     *
     * @return SimpleXmlDomInterface
     */
    protected function replaceTextWithString($string): SimpleXmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }

            return $this;
        }

        $ownerDocument = $this->node->ownerDocument;
        if ($ownerDocument) {
            $newElement = $ownerDocument->createTextNode($string);
            $newNode = $ownerDocument->importNode($newElement, true);
            $this->node->parentNode->replaceChild($newNode, $this->node);
            $this->node = $newNode;
        }

        return $this;
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
        if (
            ($strict && $value === null)
            ||
            (!$strict && empty($value))
        ) {
            /** @noinspection UnusedFunctionResultInspection */
            $this->removeAttribute($name);
        } elseif (\method_exists($this->node, 'setAttribute')) {
            /** @noinspection UnusedFunctionResultInspection */
            $this->node->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Get dom node's plain text.
     *
     * @return string
     */
    public function text(): string
    {
        return $this->getXmlDomParser()->fixHtmlOutput($this->node->textContent);
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
        return $this->getXmlDomParser()->xml($multiDecodeNewHtmlEntity, false);
    }

    /**
     * Change the name of a tag in a "DOMNode".
     *
     * @param \DOMNode $node
     * @param string   $name
     *
     * @return \DOMElement|false
     *                          <p>DOMElement a new instance of class DOMElement or false
     *                          if an error occured.</p>
     */
    protected function changeElementName(\DOMNode $node, string $name)
    {
        $ownerDocument = $node->ownerDocument;
        if (!$ownerDocument) {
            return false;
        }

        $newNode = $ownerDocument->createElement($name);

        foreach ($node->childNodes as $child) {
            $child = $ownerDocument->importNode($child, true);
            $newNode->appendChild($child);
        }

        foreach ($node->attributes ?? [] as $attrName => $attrNode) {
            /** @noinspection UnusedFunctionResultInspection */
            $newNode->setAttribute($attrName, $attrNode);
        }

        if ($newNode->ownerDocument) {
            /** @noinspection UnusedFunctionResultInspection */
            $newNode->ownerDocument->replaceChild($newNode, $node);
        }

        return $newNode;
    }

    /**
     * Returns children of node.
     *
     * @param int $idx
     *
     * @return SimpleXmlDomInterface|SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>|null
     */
    public function childNodes(int $idx = -1)
    {
        $nodeList = $this->getIterator();

        if ($idx === -1) {
            return $nodeList;
        }

        return $nodeList[$idx] ?? null;
    }

    /**
     * Find nodes with a CSS selector.
     *
     * @param string $selector
     *
     * @return SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function findMulti(string $selector): SimpleXmlDomNodeInterface
    {
        return $this->getXmlDomParser()->findMulti($selector);
    }

    /**
     * Find nodes with a CSS selector.
     *
     * @param string $selector
     *
     * @return false|SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function findMultiOrFalse(string $selector)
    {
        return $this->getXmlDomParser()->findMultiOrFalse($selector);
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
        return $this->getXmlDomParser()->findOne($selector);
    }

    /**
     * Find one node with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false|SimpleXmlDomInterface
     */
    public function findOneOrFalse(string $selector)
    {
        return $this->getXmlDomParser()->findOneOrFalse($selector);
    }

    /**
     * Returns the first child of node.
     *
     * @return SimpleXmlDomInterface|null
     */
    public function firstChild()
    {
        /** @var \DOMNode|null $node */
        $node = $this->node->firstChild;

        if ($node === null) {
            return null;
        }

        return new static($node);
    }

    /**
     * Return elements by ".class".
     *
     * @param string $class
     *
     * @return SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function getElementByClass(string $class): SimpleXmlDomNodeInterface
    {
        return $this->findMulti(".${class}");
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
        return $this->findOne("#${id}");
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
        if ($this->node instanceof \DOMElement) {
            $node = $this->node->getElementsByTagName($name)->item(0);
        } else {
            $node = null;
        }

        if ($node === null) {
            return new SimpleXmlDomBlank();
        }

        return new static($node);
    }

    /**
     * Returns elements by "#id".
     *
     * @param string   $id
     * @param int|null $idx
     *
     * @return SimpleXmlDomInterface|SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function getElementsById(string $id, $idx = null)
    {
        return $this->find("#${id}", $idx);
    }

    /**
     * Returns elements by tag name.
     *
     * @param string   $name
     * @param int|null $idx
     *
     * @return SimpleXmlDomInterface|SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function getElementsByTagName(string $name, $idx = null)
    {
        if ($this->node instanceof \DOMElement) {
            $nodesList = $this->node->getElementsByTagName($name);
        } else {
            $nodesList = [];
        }

        $elements = new SimpleXmlDomNode();

        foreach ($nodesList as $node) {
            $elements[] = new static($node);
        }

        // return all elements
        if ($idx === null) {
            if (\count($elements) === 0) {
                return new SimpleXmlDomNodeBlank();
            }

            return $elements;
        }

        // handle negative values
        if ($idx < 0) {
            $idx = \count($elements) + $idx;
        }

        // return one element
        return $elements[$idx] ?? new SimpleXmlDomBlank();
    }

    /**
     * @return \DOMNode
     */
    public function getNode(): \DOMNode
    {
        return $this->node;
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
        return $this->getXmlDomParser()->innerHtml($multiDecodeNewHtmlEntity);
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
        return !isset($this->node->nodeType);
    }

    /**
     * Returns the last child of node.
     *
     * @return SimpleXmlDomInterface|null
     */
    public function lastChild()
    {
        /** @var \DOMNode|null $node */
        $node = $this->node->lastChild;

        if ($node === null) {
            return null;
        }

        return new static($node);
    }

    /**
     * Returns the next sibling of node.
     *
     * @return SimpleXmlDomInterface|null
     */
    public function nextSibling()
    {
        /** @var \DOMNode|null $node */
        $node = $this->node->nextSibling;

        if ($node === null) {
            return null;
        }

        return new static($node);
    }

    /**
     * Returns the next sibling of node.
     *
     * @return SimpleXmlDomInterface|null
     */
    public function nextNonWhitespaceSibling()
    {
        /** @var \DOMNode|null $node */
        $node = $this->node->nextSibling;

        if ($node === null) {
            return null;
        }

        while ($node && !\trim($node->textContent)) {
            /** @var \DOMNode|null $node */
            $node = $node->nextSibling;
        }

        return new static($node);
    }

    /**
     * Returns the parent of node.
     *
     * @return SimpleXmlDomInterface
     */
    public function parentNode(): SimpleXmlDomInterface
    {
        return new static($this->node->parentNode);
    }

    /**
     * Returns the previous sibling of node.
     *
     * @return SimpleXmlDomInterface|null
     */
    public function previousSibling()
    {
        /** @var \DOMNode|null $node */
        $node = $this->node->previousSibling;

        if ($node === null) {
            return null;
        }

        return new static($node);
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
        if ($value === null) {
            if (
                $this->tag === 'input'
                &&
                (
                    $this->getAttribute('type') === 'hidden'
                    ||
                    $this->getAttribute('type') === 'text'
                    ||
                    !$this->hasAttribute('type')
                )
            ) {
                return $this->getAttribute('value');
            }

            if (
                $this->hasAttribute('checked')
                &&
                \in_array($this->getAttribute('type'), ['checkbox', 'radio'], true)
            ) {
                return $this->getAttribute('value');
            }

            if ($this->node->nodeName === 'select') {
                $valuesFromDom = [];
                $options = $this->getElementsByTagName('option');
                if ($options instanceof SimpleXmlDomNode) {
                    foreach ($options as $option) {
                        if ($this->hasAttribute('checked')) {
                            /** @noinspection UnnecessaryCastingInspection */
                            $valuesFromDom[] = (string) $option->getAttribute('value');
                        }
                    }
                }

                if (\count($valuesFromDom) === 0) {
                    return null;
                }

                return $valuesFromDom;
            }

            if ($this->node->nodeName === 'textarea') {
                return $this->node->nodeValue;
            }
        } else {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (\in_array($this->getAttribute('type'), ['checkbox', 'radio'], true)) {
                if ($value === $this->getAttribute('value')) {
                    /** @noinspection UnusedFunctionResultInspection */
                    $this->setAttribute('checked', 'checked');
                } else {
                    /** @noinspection UnusedFunctionResultInspection */
                    $this->removeAttribute('checked');
                }
            } elseif ($this->node instanceof \DOMElement && $this->node->nodeName === 'select') {
                foreach ($this->node->getElementsByTagName('option') as $option) {
                    /** @var \DOMElement $option */
                    if ($value === $option->getAttribute('value')) {
                        /** @noinspection UnusedFunctionResultInspection */
                        $option->setAttribute('selected', 'selected');
                    } else {
                        $option->removeAttribute('selected');
                    }
                }
            } elseif ($this->node->nodeName === 'input' && \is_string($value)) {
                // Set value for input elements
                /** @noinspection UnusedFunctionResultInspection */
                $this->setAttribute('value', $value);
            } elseif ($this->node->nodeName === 'textarea' && \is_string($value)) {
                $this->node->nodeValue = $value;
            }
        }

        return null;
    }

    /**
     * Retrieve an external iterator.
     *
     * @see  http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return SimpleXmlDomNode
     *                           <p>
     *                              An instance of an object implementing <b>Iterator</b> or
     *                              <b>Traversable</b>
     *                           </p>
     */
    public function getIterator(): SimpleXmlDomNodeInterface
    {
        $elements = new SimpleXmlDomNode();
        if ($this->node->hasChildNodes()) {
            foreach ($this->node->childNodes as $node) {
                $elements[] = new static($node);
            }
        }

        return $elements;
    }

    /**
     * Normalize the given input for comparision.
     *
     * @param string|XmlDomParser $input
     *
     * @return string
     */
    private function normalizeStringForComparision($input): string
    {
        if ($input instanceof XmlDomParser) {
            $string = $input->plaintext;
        } else {
            $string = (string) $input;
        }

        return
            \urlencode(
                \urldecode(
                    \trim(
                        \str_replace(
                            [
                                ' ',
                                "\n",
                                "\r",
                                '/>',
                            ],
                            [
                                '',
                                '',
                                '',
                                '>',
                            ],
                            \strtolower($string)
                        )
                    )
                )
            );
    }
}

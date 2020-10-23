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
class SimpleHtmlDom extends AbstractSimpleHtmlDom implements \IteratorAggregate, SimpleHtmlDomInterface
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
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function find(string $selector, $idx = null)
    {
        return $this->getHtmlDomParser()->find($selector, $idx);
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
                $attributes[$attr->name] = HtmlDomParser::putReplacedBackToPreserveHtmlEntities($attr->value);
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
        return $this->node && $this->node->hasAttributes();
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
            return HtmlDomParser::putReplacedBackToPreserveHtmlEntities(
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
     * Get dom node's outer html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function html(bool $multiDecodeNewHtmlEntity = false): string
    {
        return $this->getHtmlDomParser()->html($multiDecodeNewHtmlEntity);
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
        return $this->getHtmlDomParser()->innerHtml($multiDecodeNewHtmlEntity);
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
     * @return SimpleHtmlDomInterface
     */
    protected function replaceChildWithString(string $string): SimpleHtmlDomInterface
    {
        if (!empty($string)) {
            $newDocument = new HtmlDomParser($string);

            $tmpDomString = $this->normalizeStringForComparision($newDocument);
            $tmpStr = $this->normalizeStringForComparision($string);
            if ($tmpDomString !== $tmpStr) {
                throw new \RuntimeException(
                    'Not valid HTML fragment!' . "\n" .
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
            $newDocument = $this->cleanHtmlWrapper($newDocument);
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
     * @return SimpleHtmlDomInterface
     */
    protected function replaceNodeWithString(string $string): SimpleHtmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }
            $this->node = new \DOMText();

            return $this;
        }

        $newDocument = new HtmlDomParser($string);

        $tmpDomOuterTextString = $this->normalizeStringForComparision($newDocument);
        $tmpStr = $this->normalizeStringForComparision($string);
        if ($tmpDomOuterTextString !== $tmpStr) {
            throw new \RuntimeException(
                'Not valid HTML fragment!' . "\n"
                . $tmpDomOuterTextString . "\n" .
                $tmpStr
            );
        }

        $newDocument = $this->cleanHtmlWrapper($newDocument, true);
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

        // Remove head element, preserving child nodes. (again)
        if (
            $this->node->parentNode instanceof \DOMElement
            &&
            $newDocument->getIsDOMDocumentCreatedWithoutHeadWrapper()
        ) {
            $html = $this->node->parentNode->getElementsByTagName('head')[0];

            if (
                $html !== null
                &&
                $this->node->parentNode->ownerDocument
            ) {
                $fragment = $this->node->parentNode->ownerDocument->createDocumentFragment();
                /** @var \DOMNode $html */
                while ($html->childNodes->length > 0) {
                    $tmpNode = $html->childNodes->item(0);
                    if ($tmpNode !== null) {
                        /** @noinspection UnusedFunctionResultInspection */
                        $fragment->appendChild($tmpNode);
                    }
                }
                /** @noinspection UnusedFunctionResultInspection */
                $html->parentNode->replaceChild($fragment, $html);
            }
        }

        return $this;
    }

    /**
     * Replace this node with text
     *
     * @param string $string
     *
     * @return SimpleHtmlDomInterface
     */
    protected function replaceTextWithString($string): SimpleHtmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }
            $this->node = new \DOMText();

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
     * @return SimpleHtmlDomInterface
     */
    public function setAttribute(string $name, $value = null, bool $strict = false): SimpleHtmlDomInterface
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
        return $this->getHtmlDomParser()->fixHtmlOutput($this->node->textContent);
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
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface|null
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
     * @return SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function findMulti(string $selector): SimpleHtmlDomNodeInterface
    {
        return $this->getHtmlDomParser()->findMulti($selector);
    }

    /**
     * Find nodes with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function findMultiOrFalse(string $selector)
    {
        return $this->getHtmlDomParser()->findMultiOrFalse($selector);
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
        return $this->getHtmlDomParser()->findOne($selector);
    }

    /**
     * Find one node with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false|SimpleHtmlDomInterface
     */
    public function findOneOrFalse(string $selector)
    {
        return $this->getHtmlDomParser()->findOneOrFalse($selector);
    }

    /**
     * Returns the first child of node.
     *
     * @return SimpleHtmlDomInterface|null
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
     * @return SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementByClass(string $class): SimpleHtmlDomNodeInterface
    {
        return $this->findMulti(".${class}");
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
        return $this->findOne("#${id}");
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
        if ($this->node instanceof \DOMElement) {
            $node = $this->node->getElementsByTagName($name)->item(0);
        } else {
            $node = null;
        }

        if ($node === null) {
            return new SimpleHtmlDomBlank();
        }

        return new static($node);
    }

    /**
     * Returns elements by "#id".
     *
     * @param string   $id
     * @param int|null $idx
     *
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
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
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function getElementsByTagName(string $name, $idx = null)
    {
        if ($this->node instanceof \DOMElement) {
            $nodesList = $this->node->getElementsByTagName($name);
        } else {
            $nodesList = [];
        }

        $elements = new SimpleHtmlDomNode();

        foreach ($nodesList as $node) {
            $elements[] = new static($node);
        }

        // return all elements
        if ($idx === null) {
            if (\count($elements) === 0) {
                return new SimpleHtmlDomNodeBlank();
            }

            return $elements;
        }

        // handle negative values
        if ($idx < 0) {
            $idx = \count($elements) + $idx;
        }

        // return one element
        return $elements[$idx] ?? new SimpleHtmlDomBlank();
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
        return $this->node;
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
     * @return SimpleHtmlDomInterface|null
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
     * @return SimpleHtmlDomInterface|null
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
     * @return SimpleHtmlDomInterface|null
     */
    public function nextNonWhitespaceSibling()
    {
        /** @var \DOMNode|null $node */
        $node = $this->node->nextSibling;

        while ($node && !\trim($node->textContent)) {
            /** @var \DOMNode|null $node */
            $node = $node->nextSibling;
        }

        if ($node === null) {
            return null;
        }

        return new static($node);
    }

    /**
     * Returns the parent of node.
     *
     * @return SimpleHtmlDomInterface
     */
    public function parentNode(): SimpleHtmlDomInterface
    {
        return new static($this->node->parentNode);
    }

    /**
     * Returns the previous sibling of node.
     *
     * @return SimpleHtmlDomInterface|null
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
                if ($options instanceof SimpleHtmlDomNode) {
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
     * @param HtmlDomParser $newDocument
     * @param bool          $removeExtraHeadTag
     *
     * @return HtmlDomParser
     */
    protected function cleanHtmlWrapper(
        HtmlDomParser $newDocument,
        $removeExtraHeadTag = false
    ): HtmlDomParser {
        if (
            $newDocument->getIsDOMDocumentCreatedWithoutHtml()
            ||
            $newDocument->getIsDOMDocumentCreatedWithoutHtmlWrapper()
        ) {

            // Remove doc-type node.
            if ($newDocument->getDocument()->doctype !== null) {
                /** @noinspection UnusedFunctionResultInspection */
                $newDocument->getDocument()->doctype->parentNode->removeChild($newDocument->getDocument()->doctype);
            }

            // Replace html element, preserving child nodes -> but keep the html wrapper, otherwise we got other problems ...
            // so we replace it with "<simpleHtmlDomHtml>" and delete this at the ending.
            $item = $newDocument->getDocument()->getElementsByTagName('html')->item(0);
            if ($item !== null) {
                /** @noinspection UnusedFunctionResultInspection */
                $this->changeElementName($item, 'simpleHtmlDomHtml');
            }

            if ($newDocument->getIsDOMDocumentCreatedWithoutPTagWrapper()) {
                // Remove <p>-element, preserving child nodes.
                $pElement = $newDocument->getDocument()->getElementsByTagName('p')->item(0);
                if ($pElement instanceof \DOMElement) {
                    $fragment = $newDocument->getDocument()->createDocumentFragment();

                    while ($pElement->childNodes->length > 0) {
                        $tmpNode = $pElement->childNodes->item(0);
                        if ($tmpNode !== null) {
                            /** @noinspection UnusedFunctionResultInspection */
                            $fragment->appendChild($tmpNode);
                        }
                    }

                    if ($pElement->parentNode !== null) {
                        /** @noinspection UnusedFunctionResultInspection */
                        $pElement->parentNode->replaceChild($fragment, $pElement);
                    }
                }
            }

            // Remove <body>-element, preserving child nodes.
            $body = $newDocument->getDocument()->getElementsByTagName('body')->item(0);
            if ($body instanceof \DOMElement) {
                $fragment = $newDocument->getDocument()->createDocumentFragment();

                while ($body->childNodes->length > 0) {
                    $tmpNode = $body->childNodes->item(0);
                    if ($tmpNode !== null) {
                        /** @noinspection UnusedFunctionResultInspection */
                        $fragment->appendChild($tmpNode);
                    }
                }

                if ($body->parentNode !== null) {
                    /** @noinspection UnusedFunctionResultInspection */
                    $body->parentNode->replaceChild($fragment, $body);
                }
            }
        }

        // Remove head element, preserving child nodes.
        if (
            $removeExtraHeadTag
            &&
            $this->node->parentNode instanceof \DOMElement
            &&
            $newDocument->getIsDOMDocumentCreatedWithoutHeadWrapper()
        ) {
            $html = $this->node->parentNode->getElementsByTagName('head')[0] ?? null;

            if (
                $html !== null
                &&
                $this->node->parentNode->ownerDocument
            ) {
                $fragment = $this->node->parentNode->ownerDocument->createDocumentFragment();

                /** @var \DOMNode $html */
                while ($html->childNodes->length > 0) {
                    $tmpNode = $html->childNodes->item(0);
                    if ($tmpNode !== null) {
                        /** @noinspection UnusedFunctionResultInspection */
                        $fragment->appendChild($tmpNode);
                    }
                }

                /** @noinspection UnusedFunctionResultInspection */
                $html->parentNode->replaceChild($fragment, $html);
            }
        }

        return $newDocument;
    }

    /**
     * Retrieve an external iterator.
     *
     * @see  http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return SimpleHtmlDomNode
     *                           <p>
     *                              An instance of an object implementing <b>Iterator</b> or
     *                              <b>Traversable</b>
     *                           </p>
     */
    public function getIterator(): SimpleHtmlDomNodeInterface
    {
        $elements = new SimpleHtmlDomNode();
        if ($this->node->hasChildNodes()) {
            foreach ($this->node->childNodes as $node) {
                $elements[] = new static($node);
            }
        }

        return $elements;
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
        return $this->getHtmlDomParser()->innerXml($multiDecodeNewHtmlEntity);
    }

    /**
     * Normalize the given input for comparision.
     *
     * @param HtmlDomParser|string $input
     *
     * @return string
     */
    private function normalizeStringForComparision($input): string
    {
        if ($input instanceof HtmlDomParser) {
            $string = $input->outerText();

            if ($input->getIsDOMDocumentCreatedWithoutHeadWrapper()) {
                /** @noinspection HtmlRequiredTitleElement */
                $string = \str_replace(['<head>', '</head>'], '', $string);
            }
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

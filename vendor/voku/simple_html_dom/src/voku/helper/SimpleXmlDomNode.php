<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * {@inheritdoc}
 */
class SimpleXmlDomNode extends AbstractSimpleXmlDomNode implements SimpleXmlDomNodeInterface
{
    /**
     * Find list of nodes with a CSS selector.
     *
     * @param string   $selector
     * @param int|null $idx
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>|SimpleXmlDomNodeInterface[]|null
     */
    public function find(string $selector, $idx = null)
    {
        // init
        $elements = new static();

        foreach ($this as $node) {
            \assert($node instanceof SimpleXmlDomInterface);
            foreach ($node->find($selector) as $res) {
                $elements->append($res);
            }
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
        return $elements[$idx] ?? null;
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
        return $this->find($selector, null);
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
        $return = $this->find($selector, null);

        if ($return instanceof SimpleXmlDomNodeBlank) {
            return false;
        }

        return $return;
    }

    /**
     * Find one node with a CSS selector.
     *
     * @param string $selector
     *
     * @return SimpleXmlDomNodeInterface<SimpleXmlDomInterface>|null
     */
    public function findOne(string $selector)
    {
        return $this->find($selector, 0);
    }

    /**
     * Find one node with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     *
     * @return false|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    public function findOneOrFalse(string $selector)
    {
        $return = $this->find($selector, 0);

        /** @noinspection NullCoalescingOperatorCanBeUsedInspection */
        if ($return === null) {
            return false;
        }

        return $return;
    }

    /**
     * Get html of elements.
     *
     * @return string[]
     */
    public function innerHtml(): array
    {
        // init
        $html = [];

        foreach ($this as $node) {
            $html[] = $node->outertext;
        }

        return $html;
    }

    /**
     * alias for "$this->innerHtml()" (added for compatibly-reasons with v1.x)
     *
     * @return string[]
     */
    public function innertext()
    {
        return $this->innerHtml();
    }

    /**
     * alias for "$this->innerHtml()" (added for compatibly-reasons with v1.x)
     *
     * @return string[]
     */
    public function outertext()
    {
        return $this->innerHtml();
    }

    /**
     * Get plain text.
     *
     * @return string[]
     */
    public function text(): array
    {
        // init
        $text = [];

        foreach ($this as $node) {
            $text[] = $node->plaintext;
        }

        return $text;
    }
}

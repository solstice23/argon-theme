<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * {@inheritdoc}
 */
class SimpleHtmlDomNodeBlank extends AbstractSimpleHtmlDomNode implements SimpleHtmlDomNodeInterface
{
    /**
     * @param string   $selector
     * @param int|null $idx
     *
     * @return null
     */
    public function find(string $selector, $idx = null)
    {
        return null;
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
        return new self();
    }

    /**
     * Find nodes with a CSS selector.
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
     * @return null
     */
    public function findOne(string $selector)
    {
        return null;
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
     * @return string[]
     */
    public function innerHtml(): array
    {
        return [];
    }

    /**
     * alias for "$this->innerHtml()" (added for compatibly-reasons with v1.x)
     *
     * @return string[]
     */
    public function innertext()
    {
        return [];
    }

    /**
     * alias for "$this->innerHtml()" (added for compatibly-reasons with v1.x)
     *
     * @return string[]
     */
    public function outertext()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function text(): array
    {
        return [];
    }
}

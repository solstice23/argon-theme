<?php

namespace voku\helper;

interface DomParserInterface
{
    /**
     * Find list of nodes with a CSS selector.
     *
     * @param string   $selector
     * @param int|null $idx
     */
    public function find(string $selector, $idx = null);

    /**
     * Find nodes with a CSS selector.
     *
     * @param string $selector
     */
    public function findMulti(string $selector);

    /**
     * Find nodes with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     */
    public function findMultiOrFalse(string $selector);

    /**
     * Find one node with a CSS selector.
     *
     * @param string $selector
     */
    public function findOne(string $selector);

    /**
     * Find one node with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     */
    public function findOneOrFalse(string $selector);

    /**
     * @param string $content
     * @param bool   $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function fixHtmlOutput(string $content, bool $multiDecodeNewHtmlEntity = false): string;

    /**
     * @return \DOMDocument
     */
    public function getDocument(): \DOMDocument;

    /**
     * Return elements by ".class".
     *
     * @param string $class
     */
    public function getElementByClass(string $class);

    /**
     * Return element by #id.
     *
     * @param string $id
     */
    public function getElementById(string $id);

    /**
     * Return element by tag name.
     *
     * @param string $name
     */
    public function getElementByTagName(string $name);

    /**
     * Returns elements by "#id".
     *
     * @param string   $id
     * @param int|null $idx
     */
    public function getElementsById(string $id, $idx = null);

    /**
     * Returns elements by tag name.
     *
     * @param string   $name
     * @param int|null $idx
     */
    public function getElementsByTagName(string $name, $idx = null);

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
     * Get dom node's inner xml.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function innerXml(bool $multiDecodeNewHtmlEntity = false): string;

    /**
     * Load HTML from string.
     *
     * @param string   $html
     * @param int|null $libXMLExtraOptions
     *
     * @return DomParserInterface
     */
    public function loadHtml(string $html, $libXMLExtraOptions = null): self;

    /**
     * Load HTML from file.
     *
     * @param string   $filePath
     * @param int|null $libXMLExtraOptions
     *
     * @throws \RuntimeException
     *
     * @return DomParserInterface
     */
    public function loadHtmlFile(string $filePath, $libXMLExtraOptions = null): self;

    /**
     * Save the html-dom as string.
     *
     * @param string $filepath
     *
     * @return string
     */
    public function save(string $filepath = ''): string;

    /**
     * @param callable $functionName
     */
    public function set_callback($functionName);

    /**
     * Get dom node's plain text.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function text(bool $multiDecodeNewHtmlEntity = false): string;

    /**
     * Get the HTML as XML or plain XML if needed.
     *
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $htmlToXml
     * @param bool $removeXmlHeader
     * @param int  $options
     *
     * @return string
     */
    public function xml(bool $multiDecodeNewHtmlEntity = false, bool $htmlToXml = true, bool $removeXmlHeader = true, int $options = \LIBXML_NOEMPTYTAG): string;
}

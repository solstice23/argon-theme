<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * @property-read string $outerText
 *                                 <p>Get dom node's outer html (alias for "outerHtml").</p>
 * @property-read string $outerHtml
 *                                 <p>Get dom node's outer html.</p>
 * @property-read string $innerText
 *                                 <p>Get dom node's inner html (alias for "innerHtml").</p>
 * @property-read string $innerHtml
 *                                 <p>Get dom node's inner html.</p>
 * @property-read string $plaintext
 *                                 <p>Get dom node's plain text.</p>
 *
 * @method string outerText()
 *                                 <p>Get dom node's outer html (alias for "outerHtml()").</p>
 * @method string outerHtml()
 *                                 <p>Get dom node's outer html.</p>
 * @method string innerText()
 *                                 <p>Get dom node's inner html (alias for "innerHtml()").</p>
 * @method HtmlDomParser load(string $html)
 *                                 <p>Load HTML from string.</p>
 * @method HtmlDomParser load_file(string $html)
 *                                 <p>Load HTML from file.</p>
 * @method static HtmlDomParser file_get_html($filePath, $libXMLExtraOptions = null)
 *                                 <p>Load HTML from file.</p>
 * @method static HtmlDomParser str_get_html($html, $libXMLExtraOptions = null)
 *                                 <p>Load HTML from string.</p>
 */
class HtmlDomParser extends AbstractDomParser
{
    /**
     * @var string[]
     */
    protected static $functionAliases = [
        'outertext' => 'html',
        'outerhtml' => 'html',
        'innertext' => 'innerHtml',
        'innerhtml' => 'innerHtml',
        'load'      => 'loadHtml',
        'load_file' => 'loadHtmlFile',
    ];

    /**
     * @var string[]
     */
    protected $templateLogicSyntaxInSpecialScriptTags = [
        '+',
        '<%',
        '{%',
        '{{',
    ];

    /**
     * The properties specified for each special script tag is an array.
     *
     * ```php
     * protected $specialScriptTags = [
     *     'text/html',
     *     'text/x-custom-template',
     *     'text/x-handlebars-template'
     * ]
     * ```
     *
     * @var string[]
     */
    protected $specialScriptTags = [
        'text/html',
        'text/x-custom-template',
        'text/x-handlebars-template',
    ];

    /**
     * @var string[]
     */
    protected $selfClosingTags = [
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithoutHtml = false;

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithoutWrapper = false;

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithCommentWrapper = false;

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithoutHeadWrapper = false;

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithoutPTagWrapper = false;

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithoutHtmlWrapper = false;

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithoutBodyWrapper = false;

    /**
     * @var bool
     */
    protected $isDOMDocumentCreatedWithFakeEndScript = false;

    /**
     * @var bool
     */
    protected $keepBrokenHtml = false;

    /**
     * @param \DOMNode|SimpleHtmlDomInterface|string $element HTML code or SimpleHtmlDomInterface, \DOMNode
     */
    public function __construct($element = null)
    {
        $this->document = new \DOMDocument('1.0', $this->getEncoding());

        // DOMDocument settings
        $this->document->preserveWhiteSpace = true;
        $this->document->formatOutput = true;

        if ($element instanceof SimpleHtmlDomInterface) {
            $element = $element->getNode();
        }

        if ($element instanceof \DOMNode) {
            $domNode = $this->document->importNode($element, true);

            if ($domNode instanceof \DOMNode) {
                /** @noinspection UnusedFunctionResultInspection */
                $this->document->appendChild($domNode);
            }

            return;
        }

        if ($element !== null) {
            /** @noinspection UnusedFunctionResultInspection */
            $this->loadHtml($element);
        }
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        $name = \strtolower($name);

        if (isset(self::$functionAliases[$name])) {
            return \call_user_func_array([$this, self::$functionAliases[$name]], $arguments);
        }

        throw new \BadMethodCallException('Method does not exist: ' . $name);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     * @throws \RuntimeException
     *
     * @return HtmlDomParser
     */
    public static function __callStatic($name, $arguments)
    {
        $arguments0 = $arguments[0] ?? '';

        $arguments1 = $arguments[1] ?? null;

        if ($name === 'str_get_html') {
            $parser = new static();

            return $parser->loadHtml($arguments0, $arguments1);
        }

        if ($name === 'file_get_html') {
            $parser = new static();

            return $parser->loadHtmlFile($arguments0, $arguments1);
        }

        throw new \BadMethodCallException('Method does not exist');
    }

    /** @noinspection MagicMethodsValidityInspection */

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function __get($name)
    {
        $name = \strtolower($name);

        switch ($name) {
            case 'outerhtml':
            case 'outertext':
                return $this->html();
            case 'innerhtml':
            case 'innertext':
                return $this->innerHtml();
            case 'text':
            case 'plaintext':
                return $this->text();
        }

        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->html();
    }

    /**
     * does nothing (only for api-compatibility-reasons)
     *
     * @return bool
     *
     * @deprecated
     */
    public function clear(): bool
    {
        return true;
    }

    /**
     * Create DOMDocument from HTML.
     *
     * @param string   $html
     * @param int|null $libXMLExtraOptions
     *
     * @return \DOMDocument
     */
    protected function createDOMDocument(string $html, $libXMLExtraOptions = null): \DOMDocument
    {
        // Remove content before <!DOCTYPE.*> because otherwise the DOMDocument can not handle the input.
        $isDOMDocumentCreatedWithDoctype = false;
        if (\stripos($html, '<!DOCTYPE') !== false) {
            $isDOMDocumentCreatedWithDoctype = true;
            if (
                \preg_match('/(^.*?)<!(?:DOCTYPE)(?: [^>]*)?>/sui', $html, $matches_before_doctype)
                &&
                \trim($matches_before_doctype[1])
            ) {
                $html = \str_replace($matches_before_doctype[1], '', $html);
            }
        }

        if ($this->keepBrokenHtml) {
            $html = $this->keepBrokenHtml(\trim($html));
        }

        if (\strpos($html, '<') === false) {
            $this->isDOMDocumentCreatedWithoutHtml = true;
        } elseif (\strpos(\ltrim($html), '<') !== 0) {
            $this->isDOMDocumentCreatedWithoutWrapper = true;
        }

        if (\strpos(\ltrim($html), '<!--') === 0) {
            $this->isDOMDocumentCreatedWithCommentWrapper = true;
        }

        /** @noinspection HtmlRequiredLangAttribute */
        if (
            \strpos($html, '<html ') === false
            &&
            \strpos($html, '<html>') === false
        ) {
            $this->isDOMDocumentCreatedWithoutHtmlWrapper = true;
        }

        if (
            \strpos($html, '<body ') === false
            &&
            \strpos($html, '<body>') === false
        ) {
            $this->isDOMDocumentCreatedWithoutBodyWrapper = true;
        }

        /** @noinspection HtmlRequiredTitleElement */
        if (
            \strpos($html, '<head ') === false
            &&
            \strpos($html, '<head>') === false
        ) {
            $this->isDOMDocumentCreatedWithoutHeadWrapper = true;
        }

        if (
            \strpos($html, '<p ') === false
            &&
            \strpos($html, '<p>') === false
        ) {
            $this->isDOMDocumentCreatedWithoutPTagWrapper = true;
        }

        if (
            \strpos($html, '</script>') === false
            &&
            \strpos($html, '<\/script>') !== false
        ) {
            $this->isDOMDocumentCreatedWithFakeEndScript = true;
        }

        if (\stripos($html, '</html>') !== false) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (
                \preg_match('/<\/html>(.*?)/suiU', $html, $matches_after_html)
                &&
                \trim($matches_after_html[1])
            ) {
                $html = \str_replace($matches_after_html[0], $matches_after_html[1] . '</html>', $html);
            }
        }

        if (\strpos($html, '<script') !== false) {
            $this->html5FallbackForScriptTags($html);

            foreach ($this->specialScriptTags as $tag) {
                if (\strpos($html, $tag) !== false) {
                    $this->keepSpecialScriptTags($html);
                }
            }
        }

        $html = \str_replace(
            \array_map(static function ($e) {
                return '<' . $e . '>';
            }, $this->selfClosingTags),
            \array_map(static function ($e) {
                return '<' . $e . '/>';
            }, $this->selfClosingTags),
            $html
        );

        // set error level
        $internalErrors = \libxml_use_internal_errors(true);
        $disableEntityLoader = \libxml_disable_entity_loader(true);
        \libxml_clear_errors();

        $optionsXml = \LIBXML_DTDLOAD | \LIBXML_DTDATTR | \LIBXML_NONET;

        if (\defined('LIBXML_BIGLINES')) {
            $optionsXml |= \LIBXML_BIGLINES;
        }

        if (\defined('LIBXML_COMPACT')) {
            $optionsXml |= \LIBXML_COMPACT;
        }

        if (\defined('LIBXML_HTML_NODEFDTD')) {
            $optionsXml |= \LIBXML_HTML_NODEFDTD;
        }

        if ($libXMLExtraOptions !== null) {
            $optionsXml |= $libXMLExtraOptions;
        }

        if (
            $this->isDOMDocumentCreatedWithoutWrapper
            ||
            $this->isDOMDocumentCreatedWithCommentWrapper
            ||
            (
                !$isDOMDocumentCreatedWithDoctype
                &&
                $this->keepBrokenHtml
            )
        ) {
            $html = '<' . self::$domHtmlWrapperHelper . '>' . $html . '</' . self::$domHtmlWrapperHelper . '>';
        }

        $html = self::replaceToPreserveHtmlEntities($html);

        $documentFound = false;
        $sxe = \simplexml_load_string($html, \SimpleXMLElement::class, $optionsXml);
        if ($sxe !== false && \count(\libxml_get_errors()) === 0) {
            $domElementTmp = \dom_import_simplexml($sxe);
            if (
                $domElementTmp
                &&
                $domElementTmp->ownerDocument
            ) {
                $documentFound = true;
                $this->document = $domElementTmp->ownerDocument;
            }
        }

        if ($documentFound === false) {

            // UTF-8 hack: http://php.net/manual/en/domdocument.loadhtml.php#95251
            $xmlHackUsed = false;
            /** @noinspection StringFragmentMisplacedInspection */
            if (\stripos('<?xml', $html) !== 0) {
                $xmlHackUsed = true;
                $html = '<?xml encoding="' . $this->getEncoding() . '" ?>' . $html;
            }

            $this->document->loadHTML($html, $optionsXml);

            // remove the "xml-encoding" hack
            if ($xmlHackUsed) {
                foreach ($this->document->childNodes as $child) {
                    if ($child->nodeType === \XML_PI_NODE) {
                        /** @noinspection UnusedFunctionResultInspection */
                        $this->document->removeChild($child);

                        break;
                    }
                }
            }
        }

        // set encoding
        $this->document->encoding = $this->getEncoding();

        // restore lib-xml settings
        \libxml_clear_errors();
        \libxml_use_internal_errors($internalErrors);
        \libxml_disable_entity_loader($disableEntityLoader);

        return $this->document;
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
        $xPathQuery = SelectorConverter::toXPath($selector);

        $xPath = new \DOMXPath($this->document);
        $nodesList = $xPath->query($xPathQuery);
        $elements = new SimpleHtmlDomNode();

        if ($nodesList) {
            foreach ($nodesList as $node) {
                $elements[] = new SimpleHtmlDom($node);
            }
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
     * Find nodes with a CSS selector.
     *
     * @param string $selector
     *
     * @return SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function findMulti(string $selector): SimpleHtmlDomNodeInterface
    {
        return $this->find($selector, null);
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
        $return = $this->find($selector, null);

        if ($return instanceof SimpleHtmlDomNodeBlank) {
            return false;
        }

        return $return;
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
        return $this->find($selector, 0);
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
        $return = $this->find($selector, 0);

        if ($return instanceof SimpleHtmlDomBlank) {
            return false;
        }

        return $return;
    }

    /**
     * @param string $content
     * @param bool   $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function fixHtmlOutput(
        string $content,
        bool $multiDecodeNewHtmlEntity = false
    ): string {
        // INFO: DOMDocument will encapsulate plaintext into a e.g. paragraph tag (<p>),
        //          so we try to remove it here again ...

        if ($this->getIsDOMDocumentCreatedWithoutHtmlWrapper()) {
            /** @noinspection HtmlRequiredLangAttribute */
            $content = \str_replace(
                [
                    '<html>',
                    '</html>',
                ],
                '',
                $content
            );
        }

        if ($this->getIsDOMDocumentCreatedWithoutHeadWrapper()) {
            /** @noinspection HtmlRequiredTitleElement */
            $content = \str_replace(
                [
                    '<head>',
                    '</head>',
                ],
                '',
                $content
            );
        }

        if ($this->getIsDOMDocumentCreatedWithoutBodyWrapper()) {
            $content = \str_replace(
                [
                    '<body>',
                    '</body>',
                ],
                '',
                $content
            );
        }

        if ($this->getIsDOMDocumentCreatedWithFakeEndScript()) {
            $content = \str_replace(
                '</script>',
                '',
                $content
            );
        }

        if ($this->getIsDOMDocumentCreatedWithoutWrapper()) {
            $content = (string) \preg_replace('/^<p>/', '', $content);
            $content = (string) \preg_replace('/<\/p>/', '', $content);
        }

        if ($this->getIsDOMDocumentCreatedWithoutPTagWrapper()) {
            $content = \str_replace(
                [
                    '<p>',
                    '</p>',
                ],
                '',
                $content
            );
        }

        if ($this->getIsDOMDocumentCreatedWithoutHtml()) {
            $content = \str_replace(
                '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">',
                '',
                $content
            );
        }

        // https://bugs.php.net/bug.php?id=73175
        $content = \str_replace(
            \array_map(static function ($e) {
                return '</' . $e . '>';
            }, $this->selfClosingTags),
            '',
            $content
        );

        /** @noinspection HtmlRequiredTitleElement */
        $content = \trim(
            \str_replace(
                [
                    '<simpleHtmlDomHtml>',
                    '</simpleHtmlDomHtml>',
                    '<simpleHtmlDomP>',
                    '</simpleHtmlDomP>',
                    '<head><head>',
                    '</head></head>',
                ],
                [
                    '',
                    '',
                    '',
                    '',
                    '<head>',
                    '</head>',
                ],
                $content
            )
        );

        $content = $this->decodeHtmlEntity($content, $multiDecodeNewHtmlEntity);

        return self::putReplacedBackToPreserveHtmlEntities($content);
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
        $node = $this->document->getElementsByTagName($name)->item(0);

        if ($node === null) {
            return new SimpleHtmlDomBlank();
        }

        return new SimpleHtmlDom($node);
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
        $nodesList = $this->document->getElementsByTagName($name);

        $elements = new SimpleHtmlDomNode();

        foreach ($nodesList as $node) {
            $elements[] = new SimpleHtmlDom($node);
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
        return $elements[$idx] ?? new SimpleHtmlDomNodeBlank();
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
        if (static::$callback !== null) {
            \call_user_func(static::$callback, [$this]);
        }

        if ($this->getIsDOMDocumentCreatedWithoutHtmlWrapper()) {
            $content = $this->document->saveHTML($this->document->documentElement);
        } else {
            $content = $this->document->saveHTML();
        }

        if ($content === false) {
            return '';
        }

        return $this->fixHtmlOutput($content, $multiDecodeNewHtmlEntity);
    }

    /**
     * Load HTML from string.
     *
     * @param string   $html
     * @param int|null $libXMLExtraOptions
     *
     * @return HtmlDomParser
     */
    public function loadHtml(string $html, $libXMLExtraOptions = null): DomParserInterface
    {
        // reset
        self::$domBrokenReplaceHelper = [];

        $this->document = $this->createDOMDocument($html, $libXMLExtraOptions);

        return $this;
    }

    /**
     * Load HTML from file.
     *
     * @param string   $filePath
     * @param int|null $libXMLExtraOptions
     *
     * @throws \RuntimeException
     *
     * @return HtmlDomParser
     */
    public function loadHtmlFile(string $filePath, $libXMLExtraOptions = null): DomParserInterface
    {
        // reset
        self::$domBrokenReplaceHelper = [];

        if (
            !\preg_match("/^https?:\/\//i", $filePath)
            &&
            !\file_exists($filePath)
        ) {
            throw new \RuntimeException("File ${filePath} not found");
        }

        try {
            if (\class_exists('\voku\helper\UTF8')) {
                /** @noinspection PhpUndefinedClassInspection */
                $html = UTF8::file_get_contents($filePath);
            } else {
                $html = \file_get_contents($filePath);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException("Could not load file ${filePath}");
        }

        if ($html === false) {
            throw new \RuntimeException("Could not load file ${filePath}");
        }

        return $this->loadHtml($html, $libXMLExtraOptions);
    }

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
    public function xml(
        bool $multiDecodeNewHtmlEntity = false,
        bool $htmlToXml = true,
        bool $removeXmlHeader = true,
        int $options = \LIBXML_NOEMPTYTAG
    ): string {
        $xml = $this->document->saveXML(null, $options);
        if ($xml === false) {
            return '';
        }

        if ($removeXmlHeader) {
            $xml = \ltrim((string) \preg_replace('/<\?xml.*\?>/', '', $xml));
        }

        if ($htmlToXml) {
            $return = $this->fixHtmlOutput($xml, $multiDecodeNewHtmlEntity);
        } else {
            $xml = $this->decodeHtmlEntity($xml, $multiDecodeNewHtmlEntity);

            $return = self::putReplacedBackToPreserveHtmlEntities($xml);
        }

        return $return;
    }

    /**
     * @param string $selector
     * @param int    $idx
     *
     * @return SimpleHtmlDomInterface|SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
     */
    public function __invoke($selector, $idx = null)
    {
        return $this->find($selector, $idx);
    }

    /**
     * @return bool
     */
    public function getIsDOMDocumentCreatedWithoutHeadWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutHeadWrapper;
    }

    /**
     * @return bool
     */
    public function getIsDOMDocumentCreatedWithoutPTagWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutPTagWrapper;
    }

    /**
     * @return bool
     */
    public function getIsDOMDocumentCreatedWithoutHtml(): bool
    {
        return $this->isDOMDocumentCreatedWithoutHtml;
    }

    /**
     * @return bool
     */
    public function getIsDOMDocumentCreatedWithoutBodyWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutBodyWrapper;
    }

    /**
     * @return bool
     */
    public function getIsDOMDocumentCreatedWithoutHtmlWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutHtmlWrapper;
    }

    /**
     * @return bool
     */
    public function getIsDOMDocumentCreatedWithoutWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutWrapper;
    }

    /**
     * @return bool
     */
    public function getIsDOMDocumentCreatedWithFakeEndScript(): bool
    {
        return $this->isDOMDocumentCreatedWithFakeEndScript;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    protected function keepBrokenHtml(string $html): string
    {
        do {
            $original = $html;

            $html = (string) \preg_replace_callback(
                '/(?<start>.*)<(?<element_start>[a-z]+)(?<element_start_addon> [^>]*)?>(?<value>.*?)<\/(?<element_end>\2)>(?<end>.*)/sui',
                static function ($matches) {
                    return $matches['start'] .
                        '°lt_simple_html_dom__voku_°' . $matches['element_start'] . $matches['element_start_addon'] . '°gt_simple_html_dom__voku_°' .
                        $matches['value'] .
                        '°lt/_simple_html_dom__voku_°' . $matches['element_end'] . '°gt_simple_html_dom__voku_°' .
                        $matches['end'];
                },
                $html
            );
        } while ($original !== $html);

        do {
            $original = $html;

            $html = (string) \preg_replace_callback(
                '/(?<start>[^<]*)?(?<broken>(?:(?:<\/\w+(?:\s+\w+=\\"[^\"]+\\")*+)(?:[^<]+)>)+)(?<end>.*)/u',
                static function ($matches) {
                    $matches['broken'] = \str_replace(
                        ['°lt/_simple_html_dom__voku_°', '°lt_simple_html_dom__voku_°', '°gt_simple_html_dom__voku_°'],
                        ['</', '<', '>'],
                        $matches['broken']
                    );

                    self::$domBrokenReplaceHelper['orig'][] = $matches['broken'];
                    self::$domBrokenReplaceHelper['tmp'][] = $matchesHash = self::$domHtmlBrokenHtmlHelper . \crc32($matches['broken']);

                    return $matches['start'] . $matchesHash . $matches['end'];
                },
                $html
            );
        } while ($original !== $html);

        return \str_replace(
            ['°lt/_simple_html_dom__voku_°', '°lt_simple_html_dom__voku_°', '°gt_simple_html_dom__voku_°'],
            ['</', '<', '>'],
            $html
        );
    }

    /**
     * @param string $html
     *
     * @return void
     */
    protected function keepSpecialScriptTags(string &$html)
    {
        // regEx for e.g.: [<script id="elements-image-1" type="text/html">...</script>]
        $tags = \implode('|', \array_map(
            static function ($value) {
                return \preg_quote($value, '/');
            },
            $this->specialScriptTags
        ));
        $html = (string) \preg_replace_callback(
            '/(?<start>((?:<script) [^>]*type=(?:["\'])?(?:' . $tags . ')+(?:[^>]*)>))(?<innerContent>.*)(?<end><\/script>)/isU',
            function ($matches) {

                // Check for logic in special script tags, like [<% _.each(tierPrices, function(item, key) { %>],
                // because often this looks like non valid html in the template itself.
                foreach ($this->templateLogicSyntaxInSpecialScriptTags as $logicSyntaxInSpecialScriptTag) {
                    if (\strpos($matches['innerContent'], $logicSyntaxInSpecialScriptTag) !== false) {
                        // remove the html5 fallback
                        $matches['innerContent'] = \str_replace('<\/', '</', $matches['innerContent']);

                        self::$domBrokenReplaceHelper['orig'][] = $matches['innerContent'];
                        self::$domBrokenReplaceHelper['tmp'][] = $matchesHash = '' . self::$domHtmlBrokenHtmlHelper . '' . \crc32($matches['innerContent']);

                        return $matches['start'] . $matchesHash . $matches['end'];
                    }
                }

                // remove the html5 fallback
                $matches[0] = \str_replace('<\/', '</', $matches[0]);

                $specialNonScript = '<' . self::$domHtmlSpecialScriptHelper . \substr($matches[0], \strlen('<script'));

                return \substr($specialNonScript, 0, -\strlen('</script>')) . '</' . self::$domHtmlSpecialScriptHelper . '>';
            },
            $html
        );
    }

    /**
     * @param bool $keepBrokenHtml
     *
     * @return HtmlDomParser
     */
    public function useKeepBrokenHtml(bool $keepBrokenHtml): DomParserInterface
    {
        $this->keepBrokenHtml = $keepBrokenHtml;

        return $this;
    }

    /**
     * @param string[] $templateLogicSyntaxInSpecialScriptTags
     *
     * @return HtmlDomParser
     */
    public function overwriteTemplateLogicSyntaxInSpecialScriptTags(array $templateLogicSyntaxInSpecialScriptTags): DomParserInterface
    {
        foreach ($templateLogicSyntaxInSpecialScriptTags as $tmp) {
            if (!\is_string($tmp)) {
                throw new \InvalidArgumentException('setTemplateLogicSyntaxInSpecialScriptTags only allows string[]');
            }
        }

        $this->templateLogicSyntaxInSpecialScriptTags = $templateLogicSyntaxInSpecialScriptTags;

        return $this;
    }

    /**
     * @param string[] $specialScriptTags
     *
     * @return HtmlDomParser
     */
    public function overwriteSpecialScriptTags(array $specialScriptTags): DomParserInterface
    {
        foreach ($specialScriptTags as $tag) {
            if (!\is_string($tag)) {
                throw new \InvalidArgumentException('SpecialScriptTags only allows string[]');
            }
        }

        $this->specialScriptTags = $specialScriptTags;

        return $this;
    }
}

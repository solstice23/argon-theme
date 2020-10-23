<?php

declare(strict_types=1);

namespace voku\helper;

abstract class AbstractDomParser implements DomParserInterface
{
    /**
     * @var string
     */
    protected static $domHtmlWrapperHelper = '____simple_html_dom__voku__html_wrapper____';

    /**
     * @var string
     */
    protected static $domHtmlBrokenHtmlHelper = '____simple_html_dom__voku__broken_html____';

    /**
     * @var string
     */
    protected static $domHtmlSpecialScriptHelper = '____simple_html_dom__voku__html_special_script____';

    /**
     * @var array
     */
    protected static $domBrokenReplaceHelper = [];

    /**
     * @var string[][]
     */
    protected static $domLinkReplaceHelper = [
        'orig' => ['[', ']', '{', '}'],
        'tmp'  => [
            '____SIMPLE_HTML_DOM__VOKU__SQUARE_BRACKET_LEFT____',
            '____SIMPLE_HTML_DOM__VOKU__SQUARE_BRACKET_RIGHT____',
            '____SIMPLE_HTML_DOM__VOKU__BRACKET_LEFT____',
            '____SIMPLE_HTML_DOM__VOKU__BRACKET_RIGHT____',
        ],
    ];

    /**
     * @var string[][]
     */
    protected static $domReplaceHelper = [
        'orig' => ['&', '|', '+', '%', '@', '<html ⚡'],
        'tmp'  => [
            '____SIMPLE_HTML_DOM__VOKU__AMP____',
            '____SIMPLE_HTML_DOM__VOKU__PIPE____',
            '____SIMPLE_HTML_DOM__VOKU__PLUS____',
            '____SIMPLE_HTML_DOM__VOKU__PERCENT____',
            '____SIMPLE_HTML_DOM__VOKU__AT____',
            '<html ____SIMPLE_HTML_DOM__VOKU__GOOGLE_AMP____="true"',
        ],
    ];

    /**
     * @var callable|null
     */
    protected static $callback;

    /**
     * @var string[]
     */
    protected static $functionAliases = [];

    /**
     * @var \DOMDocument
     */
    protected $document;

    /**
     * @var string
     */
    protected $encoding = 'UTF-8';

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
     * @return static
     */
    abstract public static function __callStatic($name, $arguments);

    public function __clone()
    {
        $this->document = clone $this->document;
    }

    /** @noinspection MagicMethodsValidityInspection */

    /**
     * @param string $name
     *
     * @return string|null
     */
    abstract public function __get($name);

    /**
     * @return string
     */
    abstract public function __toString();

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
    abstract protected function createDOMDocument(string $html, $libXMLExtraOptions = null): \DOMDocument;

    /**
     * @param string $content
     * @param bool   $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    protected function decodeHtmlEntity(string $content, bool $multiDecodeNewHtmlEntity): string
    {
        if ($multiDecodeNewHtmlEntity) {
            if (\class_exists('\voku\helper\UTF8')) {
                /** @noinspection PhpUndefinedClassInspection */
                $content = UTF8::rawurldecode($content, true);
            } else {
                do {
                    $content_compare = $content;

                    $content = \rawurldecode(
                        \html_entity_decode(
                            $content,
                            \ENT_QUOTES | \ENT_HTML5
                        )
                    );
                } while ($content_compare !== $content);
            }
        } else {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (\class_exists('\voku\helper\UTF8')) {
                /** @noinspection PhpUndefinedClassInspection */
                $content = UTF8::rawurldecode($content, false);
            } else {
                $content = \rawurldecode(
                    \html_entity_decode(
                        $content,
                        \ENT_QUOTES | \ENT_HTML5
                    )
                );
            }
        }

        return $content;
    }

    /**
     * Find list of nodes with a CSS selector.
     *
     * @param string   $selector
     * @param int|null $idx
     */
    abstract public function find(string $selector, $idx = null);

    /**
     * Find nodes with a CSS selector.
     *
     * @param string $selector
     */
    abstract public function findMulti(string $selector);

    /**
     * Find nodes with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     */
    abstract public function findMultiOrFalse(string $selector);

    /**
     * Find one node with a CSS selector.
     *
     * @param string $selector
     */
    abstract public function findOne(string $selector);

    /**
     * Find one node with a CSS selector or false, if no element is found.
     *
     * @param string $selector
     */
    abstract public function findOneOrFalse(string $selector);

    /**
     * @return \DOMDocument
     */
    public function getDocument(): \DOMDocument
    {
        return $this->document;
    }

    /**
     * Get dom node's outer html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    abstract public function html(bool $multiDecodeNewHtmlEntity = false): string;

    /**
     * Get dom node's inner html.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function innerHtml(bool $multiDecodeNewHtmlEntity = false): string
    {
        // init
        $text = '';

        if ($this->document->documentElement) {
            foreach ($this->document->documentElement->childNodes as $node) {
                $text .= $this->document->saveHTML($node);
            }
        }

        return $this->fixHtmlOutput($text, $multiDecodeNewHtmlEntity);
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
        // init
        $text = '';

        if ($this->document->documentElement) {
            foreach ($this->document->documentElement->childNodes as $node) {
                $text .= $this->document->saveXML($node);
            }
        }

        return $this->fixHtmlOutput($text, $multiDecodeNewHtmlEntity);
    }

    /**
     * Load HTML from string.
     *
     * @param string   $html
     * @param int|null $libXMLExtraOptions
     *
     * @return DomParserInterface
     */
    abstract public function loadHtml(string $html, $libXMLExtraOptions = null): DomParserInterface;

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
    abstract public function loadHtmlFile(string $filePath, $libXMLExtraOptions = null): DomParserInterface;

    /**
     * Save the html-dom as string.
     *
     * @param string $filepath
     *
     * @return string
     */
    public function save(string $filepath = ''): string
    {
        $string = $this->html();
        if ($filepath !== '') {
            \file_put_contents($filepath, $string, \LOCK_EX);
        }

        return $string;
    }

    /**
     * @param callable $functionName
     */
    public function set_callback($functionName)
    {
        static::$callback = $functionName;
    }

    /**
     * Get dom node's plain text.
     *
     * @param bool $multiDecodeNewHtmlEntity
     *
     * @return string
     */
    public function text(bool $multiDecodeNewHtmlEntity = false): string
    {
        return $this->fixHtmlOutput($this->document->textContent, $multiDecodeNewHtmlEntity);
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
     * Get the encoding to use.
     *
     * @return string
     */
    protected function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * workaround for bug: https://bugs.php.net/bug.php?id=74628
     *
     * @param string $html
     */
    protected function html5FallbackForScriptTags(string &$html)
    {
        // regEx for e.g.: [<script id="elements-image-2">...<script>]
        /** @noinspection HtmlDeprecatedTag */
        $regExSpecialScript = '/<(script)(?<attr>[^>]*)>(?<content>.*)<\/\1>/isU';
        $htmlTmp = \preg_replace_callback(
            $regExSpecialScript,
            static function ($scripts) {
                if (empty($scripts['content'])) {
                    return $scripts[0];
                }

                return '<script' . $scripts['attr'] . '>' . \str_replace('</', '<\/', $scripts['content']) . '</script>';
            },
            $html
        );

        if ($htmlTmp !== null) {
            $html = $htmlTmp;
        }
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public static function putReplacedBackToPreserveHtmlEntities(string $html): string
    {
        static $DOM_REPLACE__HELPER_CACHE = null;

        if ($DOM_REPLACE__HELPER_CACHE === null) {
            $DOM_REPLACE__HELPER_CACHE['tmp'] = \array_merge(
                self::$domLinkReplaceHelper['tmp'],
                self::$domReplaceHelper['tmp']
            );
            $DOM_REPLACE__HELPER_CACHE['orig'] = \array_merge(
                self::$domLinkReplaceHelper['orig'],
                self::$domReplaceHelper['orig']
            );

            $DOM_REPLACE__HELPER_CACHE['tmp']['html_wrapper__start'] = '<' . self::$domHtmlWrapperHelper . '>';
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_wrapper__end'] = '</' . self::$domHtmlWrapperHelper . '>';

            $DOM_REPLACE__HELPER_CACHE['orig']['html_wrapper__start'] = '';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_wrapper__end'] = '';

            $DOM_REPLACE__HELPER_CACHE['tmp']['html_special_script__start'] = '<' . self::$domHtmlSpecialScriptHelper;
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_special_script__end'] = '</' . self::$domHtmlSpecialScriptHelper . '>';

            $DOM_REPLACE__HELPER_CACHE['orig']['html_special_script__start'] = '<script';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_special_script__end'] = '</script>';
        }

        if (
            isset(self::$domBrokenReplaceHelper['tmp'])
            &&
            \count(self::$domBrokenReplaceHelper['tmp']) > 0
        ) {
            $html = \str_ireplace(self::$domBrokenReplaceHelper['tmp'], self::$domBrokenReplaceHelper['orig'], $html);
        }

        return \str_ireplace($DOM_REPLACE__HELPER_CACHE['tmp'], $DOM_REPLACE__HELPER_CACHE['orig'], $html);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public static function replaceToPreserveHtmlEntities(string $html): string
    {
        // init
        $linksNew = [];
        $linksOld = [];

        if (\strpos($html, 'http') !== false) {

            // regEx for e.g.: [https://www.domain.de/foo.php?foobar=1&email=lars%40moelleken.org&guid=test1233312&{{foo}}#foo]
            $regExUrl = '/(\[?\bhttps?:\/\/[^\s<>]+(?:\([\w]+\)|[^[:punct:]\s]|\/|\}|\]))/i';
            \preg_match_all($regExUrl, $html, $linksOld);

            if (!empty($linksOld[1])) {
                $linksOld = $linksOld[1];
                foreach ((array) $linksOld as $linkKey => $linkOld) {
                    $linksNew[$linkKey] = \str_replace(
                        self::$domLinkReplaceHelper['orig'],
                        self::$domLinkReplaceHelper['tmp'],
                        $linkOld
                    );
                }
            }
        }

        $linksNewCount = \count($linksNew);
        if ($linksNewCount > 0 && \count($linksOld) === $linksNewCount) {
            $search = \array_merge($linksOld, self::$domReplaceHelper['orig']);
            $replace = \array_merge($linksNew, self::$domReplaceHelper['tmp']);
        } else {
            $search = self::$domReplaceHelper['orig'];
            $replace = self::$domReplaceHelper['tmp'];
        }

        return \str_replace($search, $replace, $html);
    }
}

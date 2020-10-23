<?php

declare(strict_types=1);

namespace voku\helper;

abstract class AbstractSimpleHtmlDom
{
    /**
     * @var array
     */
    protected static $functionAliases = [
        'children'     => 'childNodes',
        'first_child'  => 'firstChild',
        'last_child'   => 'lastChild',
        'next_sibling' => 'nextSibling',
        'prev_sibling' => 'previousSibling',
        'parent'       => 'parentNode',
        'outertext'    => 'html',
        'outerhtml'    => 'html',
        'innertext'    => 'innerHtml',
        'innerhtml'    => 'innerHtml',
    ];

    /**
     * @var \DOMElement|\DOMNode|null
     */
    protected $node;

    /**
     * @var SimpleHtmlAttributes|null
     */
    private $classListCache;

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
     * @param string $name
     *
     * @return SimpleHtmlAttributes|string|string[]|null
     */
    public function __get($name)
    {
        $nameOrig = $name;
        $name = \strtolower($name);

        switch ($name) {
            case 'outerhtml':
            case 'outertext':
            case 'html':
                return $this->html();
            case 'innerhtml':
            case 'innertext':
                return $this->innerHtml();
            case 'text':
            case 'plaintext':
                return $this->text();
            case 'tag':
                return $this->node ? $this->node->nodeName : '';
            case 'attr':
                return $this->getAllAttributes();
            case 'classlist':
                if ($this->classListCache === null) {
                    $this->classListCache = new SimpleHtmlAttributes($this->node ?? null, 'class');
                }

                return $this->classListCache;
            default:
                if ($this->node && \property_exists($this->node, $nameOrig)) {
                    if (\is_string($this->node->{$nameOrig})) {
                        return HtmlDomParser::putReplacedBackToPreserveHtmlEntities($this->node->{$nameOrig});
                    }

                    return $this->node->{$nameOrig};
                }

                return $this->getAttribute($name);
        }
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
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        $nameOrig = $name;
        $name = \strtolower($name);

        switch ($name) {
            case 'outertext':
            case 'outerhtml':
            case 'innertext':
            case 'innerhtml':
            case 'plaintext':
            case 'text':
            case 'tag':
                return true;
            default:
                if ($this->node && \property_exists($this->node, $nameOrig)) {
                    return isset($this->node->{$nameOrig});
                }

                return $this->hasAttribute($name);
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return SimpleHtmlDomInterface|null
     */
    public function __set($name, $value)
    {
        $nameOrig = $name;
        $name = \strtolower($name);

        switch ($name) {
            case 'outerhtml':
            case 'outertext':
                return $this->replaceNodeWithString($value);
            case 'innertext':
            case 'innerhtml':
                return $this->replaceChildWithString($value);
            case 'plaintext':
                return $this->replaceTextWithString($value);
            case 'classlist':
                $name = 'class';
                $nameOrig = 'class';
                // no break
            default:
                if ($this->node && \property_exists($this->node, $nameOrig)) {
                    return $this->node->{$nameOrig} = $value;
                }

                return $this->setAttribute($name, $value);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->html();
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function __unset($name)
    {
        /** @noinspection UnusedFunctionResultInspection */
        $this->removeAttribute($name);
    }

    /**
     * @param string $selector
     * @param int|null   $idx
     *
     * @return mixed
     */
    abstract public function find(string $selector, $idx = null);

    /**
     * @return string[]|null
     */
    abstract public function getAllAttributes();

    abstract public function getAttribute(string $name): string;

    abstract public function hasAttribute(string $name): bool;

    abstract public function html(bool $multiDecodeNewHtmlEntity = false): string;

    abstract public function innerHtml(bool $multiDecodeNewHtmlEntity = false): string;

    abstract public function removeAttribute(string $name): SimpleHtmlDomInterface;

    abstract protected function replaceChildWithString(string $string): SimpleHtmlDomInterface;

    abstract protected function replaceNodeWithString(string $string): SimpleHtmlDomInterface;

    /**
     * @param string $string
     *
     * @return SimpleHtmlDomInterface
     */
    abstract protected function replaceTextWithString($string): SimpleHtmlDomInterface;

    /**
     * @param string $name
     * @param string|null   $value
     * @param bool   $strict
     *
     * @return SimpleHtmlDomInterface
     */
    abstract public function setAttribute(string $name, $value = null, bool $strict = false): SimpleHtmlDomInterface;

    abstract public function text(): string;
}

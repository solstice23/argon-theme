<?php

declare(strict_types=1);

namespace voku\helper;

abstract class AbstractSimpleXmlDom
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
    ];

    /**
     * @var \DOMElement|\DOMNode|null
     */
    protected $node;

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
     * @param string $name
     *
     * @return array|string|null
     */
    public function __get($name)
    {
        $nameOrig = $name;
        $name = \strtolower($name);

        switch ($name) {
            case 'xml':
                return $this->xml();
            case 'plaintext':
                return $this->text();
            case 'tag':
                return $this->node ? $this->node->nodeName : '';
            case 'attr':
                return $this->getAllAttributes();
            default:
                if ($this->node && \property_exists($this->node, $nameOrig)) {
                    return $this->node->{$nameOrig};
                }

                return $this->getAttribute($name);
        }
    }

    /**
     * @param string $selector
     * @param int|null    $idx
     *
     * @return SimpleXmlDomInterface|SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
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
     * @return SimpleXmlDomInterface|null
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
        return $this->xml();
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
     * @return SimpleXmlDomInterface|SimpleXmlDomInterface[]|SimpleXmlDomNodeInterface<SimpleXmlDomInterface>
     */
    abstract public function find(string $selector, $idx = null);

    /**
     * @return string[]|null
     */
    abstract public function getAllAttributes();

    /**
     * @param string $name
     *
     * @return string
     */
    abstract public function getAttribute(string $name): string;

    /**
     * @param string $name
     *
     * @return bool
     */
    abstract public function hasAttribute(string $name): bool;

    abstract public function innerXml(bool $multiDecodeNewHtmlEntity = false): string;

    abstract public function removeAttribute(string $name): SimpleXmlDomInterface;

    abstract protected function replaceChildWithString(string $string): SimpleXmlDomInterface;

    abstract protected function replaceNodeWithString(string $string): SimpleXmlDomInterface;

    /**
     * @param string $string
     *
     * @return SimpleXmlDomInterface
     */
    abstract protected function replaceTextWithString($string): SimpleXmlDomInterface;

    /**
     * @param string $name
     * @param string|null   $value
     * @param bool   $strict
     *
     * @return SimpleXmlDomInterface
     */
    abstract public function setAttribute(string $name, $value = null, bool $strict = false): SimpleXmlDomInterface;

    abstract public function text(): string;

    abstract public function xml(bool $multiDecodeNewHtmlEntity = false): string;
}

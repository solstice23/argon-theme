<?php

namespace voku\helper;

/**
 * Represents a set of space-separated attributes of an element attribute.
 *
 * @property-read int    $length The number of tokens.
 * @property-read string $value  A space-separated list of the tokens.
 */
interface SimpleHtmlAttributesInterface
{
    /**
     * Adds the given tokens to the list.
     *
     * @param string ...$tokens
     *                          <p>The tokens you want to add to the list.</p>
     *
     * @return \DOMAttr|false|null
     */
    public function add(string ...$tokens);

    /**
     * Returns true if the list contains the given token, otherwise false.
     *
     * @param string $token the token you want to check for the existence of in the list
     *
     * @return bool true if the list contains the given token, otherwise false
     */
    public function contains(string $token): bool;

    /**
     * Returns an iterator allowing you to go through all tokens contained in the list.
     *
     * @return \ArrayIterator
     */
    public function entries(): \ArrayIterator;

    /**
     * Returns an item in the list by its index (returns null if the number is greater than or equal to the length of
     * the list).
     *
     * @param int $index the zero-based index of the item you want to return
     *
     * @return string|null
     */
    public function item(int $index);

    /**
     * Removes the specified tokens from the list. If the string does not exist in the list, no error is thrown.
     *
     * @param string ...$tokens
     *                           <p>The token you want to remove from the list.</>
     *
     * @return \DOMAttr|false|null
     */
    public function remove(string ...$tokens);

    /**
     * Replaces an existing token with a new token.
     *
     * @param string $old the token you want to replace
     * @param string $new the token you want to replace $old with
     *
     * @return \DOMAttr|false|null
     */
    public function replace(string $old, string $new);

    /**
     * Removes a given token from the list and returns false. If token doesn't exist it's added and the function
     * returns true.
     *
     * @param string $token the token you want to toggle
     * @param bool   $force A Boolean that, if included, turns the toggle into a one way-only operation. If set to
     *                      false, the token will only be removed but not added again. If set to true, the token will
     *                      only be added but not removed again.
     *
     * @return bool false if the token is not in the list after the call, or true if the token is in the list after the
     *              call
     */
    public function toggle(string $token, bool $force = null): bool;
}

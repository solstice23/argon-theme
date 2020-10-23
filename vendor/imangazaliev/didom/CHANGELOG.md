### 1.13

- Add `Element::outerHtml()` method
- Add `Element::prependChild()` method
- Add `Element::insertBefore()` and `Element::insertAfter()` methods
- Add `Element::style()` method for more convenient inline styles manipulation
- Add `Element::classes()` method for more convenient class manipulation

### 1.12

- Many fixes and improvements

### 1.11.1

- Fix bug with unregistered PHP functions in XPath in `Document::has()` and `Document::count()` methods

### 1.11

- Add `Element::isElementNode()` method
- Add ability to retrieve only specific attributes in `Element::attributes()` method
- Add `Element::removeAllAttributes()` method
- Add ability to specify selector and node type in `Element::previousSibling()` and `Element::nextSibling()` methods
- Add `Element::previousSiblings()` and `Element::nextSiblings()` methods
- Many minor fixes and improvements

### 1.10.6

- Fix bug with XML document loading

### v1.10.5

- Fix issue #85

### 1.10.4

- Use `mb_convert_encoding()` in the Encoder if it is available

### v1.10.3

- Add `Element::removeChild()` and `Element::removeChildren()` methods
- Fix bug in `Element::matches()` method
- `Element::matches()` method now returns false if node is not `DOMElement`
- Add `Element::hasChildren()` method

### 1.10.2

- Fix bug in setInnerHtml: can't rewrite existing content
- Throw `InvalidSelectorException` instead of `InvalidArgumentException` when selector is empty

### 1.10.1

- Fix attributes `ends-with` XPath
- Method `Element::matches()` now can check children nodes

### 1.10

- Fix HTML saving mechanism
- Throw `InvalidSelectorException` instead of `RuntimeException` in Query class

### 1.9.1

- Add ability to search in owner document using current node as context
- Bugs fixed

### 1.9.0

- Methods `Document::appendChild()` and `Element::appendChild()` now return appended node(s)
- Add ability to search elements in context

### 1.8.8

- Bugs fixed

### 1.8.7

- Add `Element::getLineNo()` method

### 1.8.6

- Fix issue #55

### 1.8.5

- Add support of `DOMComment`

### 1.8.4

- Add ability to create an element by selector
- Add closest method

### 1.8.3

- Add method `Element::isTextNode()`
- Many minor fixes

### 1.8.2

- Add ability to check that element matches selector
- Add ability counting nodes by selector
- Many minor fixes

### 1.8.1

- Small fix

### 1.8

- Bug fixes
- Add support of ~ selector
- Add ability to direct search by CSS selector
- Add setInnerHtml method
- Add attributes method

### 1.7.4

- Add support of text nodes

### 1.7.3

- Bug fix

### 1.7.2

- Fixed behavior of nth-child pseudo class
- Add nth-of-type pseudo class

### 1.7.1

- Add pseudo class has and more attribute options

### 1.7.0

- Bug fixes
- Add methods `previousSibling`, `nextSibling`, `child`, `firstChild`, `lastChild`, `children`, `getDocument` to the Element
- Changed behavior of parent method. Now it returns parent node instead of owner document

### 1.6.8

- Bug fix

### 1.6.5

- Added ability to get an element attribute by CSS selector

### 1.6.4

- Added handling of `DOMText` and `DOMAttr` in `Document::find()`

### 1.6.3

- Added ability to get inner HTML

### 1.6.2

- Added the ability to pass options when load HTML or XML

### 1.6.1

- Added the ability to pass an array of nodes to appendChild
- Added the ability to pass options when converting to HTML or XML
- Added the ability to add child elements to the element

### 1.6

- Added support for XML
- Added the ability to search element by part of attribute name or value
- Added support for pseudo-class "contains"
- Added the ability to clone a node

### 1.5.1

- Added ability to remove and replace nodes
- Added ability to specify encoding when converting the element into the document

### 1.5

- Fixed problem with incorrect encoding
- Added ability to set the value of the element
- Added ability to specify encoding when creating document

### 1.4

- Added the ability to specify the return type element (`DiDom\Element` or `DOMElement`)

### 1.3.2

- Bug fixed

### 1.3.1

- Bugs fixed
- Added the ability to pass element attributes in the constructor

### 1.3

- Bugs fixed

### 1.2

- Bugs fixed
- Added the ability to compare Element\Document
- Added the ability to format HTML code of the document when outputting

### 1.1

- Added cache control
- Converter from CSS to XPath replaced by faster

### 1.0

- First release
# DiDOM

[![Build Status](https://travis-ci.org/Imangazaliev/DiDOM.svg?branch=master)](https://travis-ci.org/Imangazaliev/DiDOM)
[![Total Downloads](https://poser.pugx.org/imangazaliev/didom/downloads)](https://packagist.org/packages/imangazaliev/didom)
[![Latest Stable Version](https://poser.pugx.org/imangazaliev/didom/v/stable)](https://packagist.org/packages/imangazaliev/didom)
[![License](https://poser.pugx.org/imangazaliev/didom/license)](https://packagist.org/packages/imangazaliev/didom)

[English version](README.md)

DiDOM - простая и быстрая библиотека для парсинга HTML.

## Содержание

- [Установка](#Установка)
- [Быстрый старт](#Быстрый-старт)
- [Создание нового документа](#Создание-нового-документа)
- [Поиск элементов](#Поиск-элементов)
- [Проверка наличия элемента](#Проверка-наличия-элемента)
- [Подсчет количества элементов](#Подсчет-количества-элементов)
- [Поиск в элементе](#Поиск-в-элементе)
- [Поддерживамые селекторы](#Поддерживамые-селекторы)
- [Изменение содержимого](#Изменение-содержимого)
- [Вывод содержимого](#Вывод-содержимого)
- [Работа с элементами](#Работа-с-элементами)
    - [Создание нового элемента](#Создание-нового-элемента)
    - [Получение названия элемента](#Получение-названия-элемента)
    - [Получение родительского элемента](#Получение-родительского-элемента)
    - [Получение соседних элементов](#Получение-соседних-элементов)
    - [Получение дочерних элементов](#Получение-соседних-элементов)
    - [Получение документа](#Получение-документа)
    - [Работа с атрибутами элемента](#Работа-с-атрибутами-элемента)
    - [Сравнение элементов](#Сравнение-элементов)
    - [Добавление дочерних элементов](#Добавление-дочерних-элементов)
    - [Замена элемента](#Замена-элемента)
    - [Удаление элемента](#Удаление-элемента)
- [Работа с кэшем](#Работа-с-кэшем)
- [Прочее](#Прочее)
- [Сравнение с другими парсерами](#Сравнение-с-другими-парсерами)

## Установка

Для установки DiDOM выполните команду:

    composer require imangazaliev/didom

## Быстрый старт

```php
use DiDom\Document;

$document = new Document('http://www.news.com/', true);

$posts = $document->find('.post');

foreach($posts as $post) {
    echo $post->text(), "\n";
}
```

## Создание нового документа

DiDom позволяет загрузить HTML несколькими способами:

##### Через конструктор

```php
// в первом параметре передается строка с HTML
$document = new Document($html);

// путь к файлу
$document = new Document('page.html', true);

// или URL
$document = new Document('http://www.example.com/', true);

// также можно создать документ из DOMDocument
$domDocument = new DOMDocument();
$document = new Document($domDocument);
```

Сигнатура:

```php
__construct($string = null, $isFile = false, $encoding = 'UTF-8', $type = Document::TYPE_HTML)
```

`$isFile` - указывает, что загружается файл. По умолчанию - `false`.

`$encoding` - кодировка документа. По умолчанию - UTF-8.

`$type` - тип документа (HTML - `Document::TYPE_HTML`, XML - `Document::TYPE_XML`). По умолчанию - `Document::TYPE_HTML`.

##### Через отдельные методы

```php
$document = new Document();

$document->loadHtml($html);

$document->loadHtmlFile('page.html');

$document->loadHtmlFile('http://www.example.com/');
```

Для загрузки XML есть соответствующие методы `loadXml` и `loadXmlFile`.

При загрузке документа через эти методы, парсеру можно передать дополнительные [опции](http://php.net/manual/ru/libxml.constants.php):

```php
$document->loadHtml($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
$document->loadHtmlFile($url, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

$document->loadXml($xml, LIBXML_PARSEHUGE);
$document->loadXmlFile($url, LIBXML_PARSEHUGE);
```

## Поиск элементов

В качестве выражения для поиска можно передать CSS-селектор или XPath. Для этого в первом параметре нужно передать само выражение, а во втором - его тип (по умолчанию - `Query::TYPE_CSS`):

##### Через метод `find()`:

```php
use DiDom\Document;
use DiDom\Query;

...

// CSS-селектор
$posts = $document->find('.post');

// эквивалентно
$posts = $document->find('.post', Query::TYPE_CSS);

// XPath-выражение
$posts = $document->find("//div[contains(@class, 'post')]", Query::TYPE_XPATH);
```

Метод вернет массив с элементами (экземпляры класса `DiDom\Element`) или пустой массив, если не найден ни один элемент, соответствующий выражению.

При желании можно получить массив узлов без преобразования в Element или текст (`DOMElement`/`DOMText`/`DOMComment`/`DOMAttr`, в зависимости от выражения), для этого необходимо передать в качестве третьего параметра `false`.

##### Через метод `first()`:

Возвращает первый найденный элемент или `null`, если не найдено ни одного элемента.

Принимает те же параметры, что и метод `find()`.

##### Через магический метод `__invoke()`:

```php
$posts = $document('.post');
```

Принимает те же параметры, что и метод `find()`.

**Внимание:** использование данного метода нежелательно, т.к. в будущем он может быть удален.

##### Через метод `xpath()`:

```php
$posts = $document->xpath("//*[contains(concat(' ', normalize-space(@class), ' '), ' post ')]");
```

## Проверка наличия элемента

Проверить наличие элемента можно с помощью метода `has()`:

```php
if ($document->has('.post')) {
    // код
}
```

Если нужно проверить наличие элемента, а затем получить его, то можно сделать так:

```php
if ($document->has('.post')) {
    $elements = $document->find('.post');

    // код
}
```

но быстрее так:

```php
$elements = $document->find('.post');

if (count($elements) > 0) {
    // код
}
```

т.к. в первом случае выполняется два запроса.

## Подсчет количества элементов

Метод `count()` позволяет подсчитать количество дочерних элементов, соотвествующих селектору:

```php
// выведет количество ссылок в документе
echo $document->count('a');
```

```php
// выведет количество пунктов в списке
echo $document->first('ul')->count('> li');
```

## Поиск в элементе

Методы `find()`, `first()`, `xpath()`, `has()`, `count()` доступны также и для элемента.

Пример:

```php
echo $document->find('nav')[0]->first('ul.menu')->xpath('//li')[0]->text();
```

#### Метод `findInDocument()`

При изменении, замене или удалении элемента, найденного в другом элементе, документ не будет изменен. Данное поведение связано с тем, что в методе `find()` класса `Element` (а, соответственно, и в методах `first()` и `xpath`) создается новый документ, в котором и производится поиск.

Для поиска элементов в исходном документе необходимо использовать методы `findInDocument()` и `firstInDocument()`:

```php
// ничего не выйдет
$document->first('head')->first('title')->remove();

// а вот так да
$document->first('head')->firstInDocument('title')->remove();
```

**Внимание:** методы `findInDocument()` и `firstInDocument()` работают только для элементов, которые принадлежат какому-либо документу, либо созданых через `new Element(...)`. Если элемент не принадлежит к какому-либо документу, будет выброшено исключение `LogicException`;

## Поддерживамые селекторы

DiDom поддерживает поиск по:

- тэгу
- классу, идентификатору, имени и значению атрибута
- псевдоклассам:
    - first-, last-, nth-child
    - empty и not-empty
    - contains
    - has

```php
// все ссылки
$document->find('a');

// любой элемент с id = "foo" и классом "bar"
$document->find('#foo.bar');

// любой элемент, у которого есть атрибут "name"
$document->find('[name]');

// эквивалентно
$document->find('*[name]');

// поле ввода с именем "foo"
$document->find('input[name=foo]');
$document->find('input[name=\'foo\']');
$document->find('input[name="foo"]');

// поле ввода с именем "foo" и значением "bar"
$document->find('input[name="foo"][value="bar"]');

// поле ввода, название которого НЕ равно "foo"
$document->find('input[name!="foo"]');

// любой элемент, у которого есть атрибут,
// начинающийся с "data-" и равный "foo"
$document->find('*[^data-=foo]');

// все ссылки, у которых адрес начинается с https
$document->find('a[href^=https]');

// все изображения с расширением png
$document->find('img[src$=png]');

// все ссылки, содержащие в своем адресе строку "example.com"
$document->find('a[href*=example.com]');

// все ссылки, содержащие в атрибуте data-foo значение bar отделенное пробелом
$document->find('a[data-foo~=bar]');

// текст всех ссылок с классом "foo" (массив строк)
$document->find('a.foo::text');

// эквивалентно
$document->find('a.foo::text()');

// адрес и текст подсказки всех полей с классом "bar"
$document->find('a.bar::attr(href|title)');

// все ссылки, которые являются прямыми потомками текущего элемента
$element->find('> a');
```

## Изменение содержимого

### Изменение HTML

```php
$element->setInnerHtml('<a href="#">Foo</a>');
```

### Изменение значения

```php
$element->setValue('Foo');
```

## Вывод содержимого

### Получение HTML

##### Через метод `html()`:

```php
// HTML-код документа
echo $document->html();

// HTML-код элемента
echo $document->first('.post')->html();
```

##### Приведение к строке:

```php
// HTML-код документа
$html = (string) $document;

// HTML-код элемента
$html = (string) $document->first('.post');
```

**Внимание:** использование данного способа нежелательно, т.к. в будущем он может быть удален.

##### Форматирование HTML при выводе

```php
echo $document->format()->html();
```

Метод `format()` отсутствует у элемента, поэтому, если нужно получить отформатированный HTML-код элемента, необходимо сначала преобразовать его в документ:

```php
$html = $element->toDocument()->format()->html();
```

#### Внутренний HTML

```php
$innerHtml = $element->innerHtml();
```

Метод `innerHtml()` отсутствует у документа, поэтому, если нужно получить внутренний HTML-код документа, необходимо сначала преобразовать его в элемент:

```php
$innerHtml = $document->toElement()->innerHtml();
```

### Получение XML

```php
// XML-код документа
echo $document->xml();

// XML-код элемента
echo $document->first('book')->xml();
```

### Получение содержимого

Возвращает текстовое содержимое узла и его потомков:

```php
echo $element->text();
```

## Создание нового элемента

### Создание экземпляра класса

```php
use DiDom\Element;

$element = new Element('span', 'Hello');

// выведет "<span>Hello</span>"
echo $element->html();
```

Первым параметром передается название элемента, вторым - его значение (необязательно), третьим - атрибуты элемента (необязательно).

Пример создания элемента с атрибутами:

```php
$attributes = ['name' => 'description', 'placeholder' => 'Enter description of item'];

$element = new Element('textarea', 'Text', $attributes);
```

Элемент можно создать и из экземпляра класса `DOMElement`:

```php
use DiDom\Element;
use DOMElement;

$domElement = new DOMElement('span', 'Hello');
$element = new Element($domElement);
```

#### Изменение элемента, созданного из `DOMElement`

Экземпляры класса `DOMElement`, созданные через конструктор (`new DOMElement(...)`), являются неизменяемыми, поэтому и элементы (экземпляры класса `DiDom\Element`), созданные из таких объектов, так же являются неизменяемыми.

Пример:

```php
$element = new Element('span', 'Hello');

// добавит атрибут "id" со значением "greeting"
$element->attr('id', 'greeting');

$domElement = new DOMElement('span', 'Hello');
$element = new Element($domElement);

// будет выброшено исключение
// DOMException with message 'No Modification Allowed Error'
$element->attr('id', 'greeting');
```

### С помощью метода `Document::createElement()`

```php
$document = new Document($html);

$element = $document->createElement('span', 'Hello');
```

### С помощью CSS-селектора

Первый параметр - селектор, второй - значение, третий - массив с атрибутами.

Атрибуты элемента могут быть указаны как в селекторе, так и переданы отдельно в третьем параметре.

Если название атрибута в массиве совпадает с названием атрибута из селектора, будет использовано значение, указанное в селекторе.

```php
$document = new Document($html);

$element = $document->createElementBySelector('div.block', 'Foo', [
    'id' => '#content',
    'class' => '.container',
]);
```

Можно так же использовать статический метод `createBySelector` класса `Element`:

```php
$element = Element::createBySelector('div.block', 'Foo', [
     'id' => '#content',
     'class' => '.container',
 ]);
```

## Получение названия элемента

```php
$element->tag;
```

## Получение родительского элемента

```php
$element->parent();
```

Так же можно получить родительский элемент, соответствующий селектору:

```php
$element->closest('.foo');
```

Вернет родительский элемент, у которого есть класс `foo`. Если подходящий элемент не найден, метод вернет `null`.

## Получение соседних элементов

Первый аргумент - CSS-селектор, второй - тип узла (`DOMElement`, `DOMText` или `DOMComment`).

Если оба аргумента опущены, будет осуществлен поиск узлов любого типа.

Если селектор указан, а тип узла нет, будет использован тип `DOMElement`.

**Внимание:** Селектор можно использовать только с типом `DOMElement`.

```php
// предыдущий элемент
$item->previousSibling();

// предыдущий элемент, соответствующий селектору
$item->previousSibling('span');

// предыдущий элемент типа DOMElement
$item->previousSibling(null, 'DOMElement');

// предыдущий элемент типа DOMComment
$item->previousSibling(null, 'DOMComment');
```

```php
// все предыдущие элементы
$item->previousSiblings();

// все предыдущие элементы, соответствующие селектору
$item->previousSiblings('span');

// все предыдущие элементы типа DOMElement
$item->previousSiblings(null, 'DOMElement');

// все предыдущие элементы типа DOMComment
$item->previousSiblings(null, 'DOMComment');
```

```php
// следующий элемент
$item->nextSibling();

// следующий элемент, соответствующий селектору
$item->nextSibling('span');

// следующий элемент типа DOMElement
$item->nextSibling(null, 'DOMElement');

// следующий элемент типа DOMComment
$item->nextSibling(null, 'DOMComment');
```

```php
// все последующие элементы
$item->nextSiblings();

// все последующие элементы, соответствующие селектору
$item->nextSiblings('span');

// все последующие элементы типа DOMElement
$item->nextSiblings(null, 'DOMElement');

// все последующие элементы типа DOMComment
$item->nextSiblings(null, 'DOMComment');
```

## Получение дочерних элементов

```php
$html = '<div>Foo<span>Bar</span><!--Baz--></div>';

$document = new Document($html);

$div = $document->first('div');

// элемент (DOMElement)
// string(3) "Bar"
var_dump($div->child(1)->text());

// текстовый узел (DOMText)
// string(3) "Foo"
var_dump($div->firstChild()->text());

// комментарий (DOMComment)
// string(3) "Baz"
var_dump($div->lastChild()->text());

// array(3) { ... }
var_dump($div->children());
```

## Получение документа

```php
$document = new Document($html);

$element = $document->first('input[name=email]');

$document2 = $element->getDocument();

// bool(true)
var_dump($document->is($document2));
```

## Работа с атрибутами элемента

#### Создание/изменение атрибута

##### Через метод `setAttribute`:
```php
$element->setAttribute('name', 'username');
```

##### Через метод `attr`:
```php
$element->attr('name', 'username');
```

##### Через магический метод `__set`:
```php
$element->name = 'username';
```

#### Получение значения атрибута

##### Через метод `getAttribute`:
```php
$username = $element->getAttribute('value');
```

##### Через метод `attr`:
```php
$username = $element->attr('value');
```

##### Через магический метод `__get`:
```php
$username = $element->name;
```

Если атрибут не найден, вернет `null`.

#### Проверка наличия атрибута

##### Через метод `hasAttribute`:
```php
if ($element->hasAttribute('name')) {
    // код
}
```

##### Через магический метод `__isset`:
```php
if (isset($element->name)) {
    // код
}
```

#### Удаление атрибута:

##### Через метод `removeAttribute`:
```php
$element->removeAttribute('name');
```

##### Через магический метод `__unset`:
```php
unset($element->name);
```

#### Получение всех атрибутов:

```php
var_dump($element->attributes());
```

#### Получение определенных атрибутов:

```php
var_dump($element->attributes(['name', 'type']));
```

#### Удаление всех атрибутов:

```php
$element->removeAllAttributes();
```

#### Удаление всех атрибутов, за исключением указанных:

```php
$element->removeAllAttributes(['name', 'type']);
```

## Сравнение элементов

```php
$element  = new Element('span', 'hello');
$element2 = new Element('span', 'hello');

// bool(true)
var_dump($element->is($element));

// bool(false)
var_dump($element->is($element2));
```

## Добавление дочерних элементов

```php
$list = new Element('ul');

$item = new Element('li', 'Item 1');

$list->appendChild($item);

$items = [
    new Element('li', 'Item 2'),
    new Element('li', 'Item 3'),
];

$list->appendChild($items);
```

## Замена элемента

```php
$title = new Element('title', 'foo');

$document->first('title')->replace($title);
```

**Внимание:** заменить можно только те элементы, которые были найдены непосредственно в документе:

```php
// ничего не выйдет
$document->first('head')->first('title')->replace($title);

// а вот так да
$document->first('head title')->replace($title);
```

Подробнее об этом в разделе [Поиск в элементе](#Поиск-в-элементе).

## Удаление элемента

```php
$document->first('title')->remove();
```

**Внимание:** удалить можно только те элементы, которые были найдены непосредственно в документе:

```php
// ничего не выйдет
$document->first('head')->first('title')->remove();

// а вот так да
$document->first('head title')->remove();
```

Подробнее об этом в разделе [Поиск в элементе](#Поиск-в-элементе).

## Работа с кэшем

Кэш - массив XPath-выражений, полученных из CSS.

#### Получение кэша

```php
use DiDom\Query;

...

$xpath    = Query::compile('h2');
$compiled = Query::getCompiled();

// array('h2' => '//h2')
var_dump($compiled);
```

#### Установка кэша

```php
Query::setCompiled(['h2' => '//h2']);
```

## Прочее

#### `preserveWhiteSpace`

По умолчанию сохранение пробелов между тегами отключено.

Включать опцию `preserveWhiteSpace` следует до загрузки документа:

```php
$document = new Document();

$document->preserveWhiteSpace();

$document->loadXml($xml);
```

#### `matches`

Возвращает `true`, если элемент соответсвует селектору:

```php
// вернет true, если элемент это div с идентификатором content
$element->matches('div#content');

// строгое соответствие
// вернет true, если элемент это div с идентификатором content и ничего более
// если у элемента будут какие-либо другие атрибуты, метод вернет false
$element->matches('div#content', true);
```

#### `isElementNode`

Проверяет, является ли элемент узлом типа DOMElement:

```php
$element->isElementNode();
```

#### `isTextNode`

Проверяет, является ли элемент текстовым узлом (DOMText):

```php
$element->isTextNode();
```

#### `isCommentNode`

Проверяет, является ли элемент комментарием (DOMComment):

```php
$element->isCommentNode();
```

## Сравнение с другими парсерами

[Сравнение с другими парсерами](https://github.com/Imangazaliev/DiDOM/wiki/Сравнение-с-другими-парсерами-(1.6.3))

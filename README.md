# PHPUnit result printer

This alternative PHPUnit result printer provides a compact test result presentation.

![Screenshot](https://raw.githubusercontent.com/bus-factor/phpunit-result-printer/master/screenshot.png)

## Installation

Add the following in your ```phpunit.xml```:

```
<phpunit
    printerFile="./vendor/bus-factor/phpunit-result-printer/src/ResultPrinter.php"
    printerClass="PhpunitResultPrinter\ResultPrinter"
    >
```

# PHPUnit result printer

This alternative PHPUnit result printer provides a compact test result presentation.

## Installation

Add the following in your ```phpunit.xml```:

```
<phpunit
    printerFile="./vendor/bus-factor/phpunit-result-printer/src/ResultPrinter.php"
    printerClass="PhpUnitResultPrinter\ResultPrinter"
    >
```

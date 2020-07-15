## Użycie konwertera

### 1. Instalujemy paczke przez composera:
```text
composer require liderit/ramowka-import-tv-republika "^1.0.0"
```

### 2. Pobieramy plik

```php
$docPath = $_FILES["documentFile"]["tmp_name"];
```

### 3. Tworzymy instancje konwertera i importujemy dokument

```php
use RamowkaTvRepublika\RamowkaTvRepublika;

try {
  $processor = new RamowkaTvRepublika($_ENV);
  $results = $processor->importDocument($docPath);
} catch(Exception $e) {
  echo "Błąd";
}
```

### 4. Obsługujemy wynik

Wynik konwersji jest przedstawiony w postaci kolekcji o następującym schemacie:

```text
[
  "worksheetsCount" => number, // liczba wszystkich arkuszy
  "processedWorksheetsCount" => number, // liczba przetworzonych arkuszy
  "totalProcessedRows" => number // liczba przetworzonych wierszy ze wszystkich arkuszy łącznie
]
```

Przykład użycia:

```php
<p>Zaimportowano <b><?php echo $data["processedWorksheetsCount"] . " / " . $data["worksheetsCount"] ?></b> arkuszy.</p>
<p>Podczas importu przetworzono <b><?php echo $data["totalProcessedRows"] ?></b> wierszy.</p>
```

Ilość przerobionych arkuszy (`processedWorksheetsCount`), a faktycznej ilości arkuszy (`worksheetsCount`) jest również wskaźnikiem, że coś poszło nie tak,
ale mechanizm nie mógł wychwycić błędu. Warto sprawdzić również te zmienne.

```php
if($data["processedWorksheetsCount"] !== $data["worksheetsCount"]) {
  echo "Błąd";
}
```

### 5. Zaawansowana obsługa błędów

System udostepnia również możliwość wychwytywania gdzie wystąpił błąd i z jakiego powodu.

```php
use RamowkaTvRepublika\Exceptions\SpreadsheetReadException;
use RamowkaTvRepublika\RamowkaTvRepublika;
use RamowkaTvRepublika\SpreadsheetReadExceptionType;

try {

  $processor = new RamowkaTvRepublika($_ENV);
  $results = $processor->importDocument($docPath);

} catch(SpreadsheetReadException $e) {

  switch($e->getExceptionType()) {
    case SpreadsheetReadExceptionType::WHOLE_DOCUMENT_ERROR: {
      echo "Błąd odczytu pliku, bądź wewętrzny błąd serwera.";
      break;
    }
    case SpreadsheetReadExceptionType::SINGLE_WORKSHEET_ERROR: {
      echo "Wskazany plik posiada nieznany błąd w arkuszu o nazwie `{$e->getWorksheetName()}`.";
      break;
    }
    case SpreadsheetReadExceptionType::SINGLE_ROW_ERROR: {
      echo "Wskazany plik posiada błąd w arkuszu o nazwie `{$e->getWorksheetName()}` w wierszu o numerze `{$e->getRow()}`.";
      break;
    }
  }

} catch(Exception $e) {
  throw $e;
}
```

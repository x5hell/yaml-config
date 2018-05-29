# yaml_config

* Модуль позволяет сгенерировать ООП код конфига из
yaml-файла.

Например, из **yaml-файла**:

```yaml
family: # семья
  father: # отец
    name: Bob # имя
    hobby: # хобби
      - sport # спорт
      - boardgames # настольные игры
    story: | # биография
      родился в Бафало: # место рождения
      учился в церковно-приходской школе
  doter: # дочь
    name: Mila # имя
    age: # возраст
      '2017-04-17': 0
      '2018-04-17': 1
      '2019-04-17': 2
```

* **модуль** сгенерирует php-код, который,
будет позволять обращаться
к значениям конфига в ООП-стиле:

```php
$config = new Config($date);
$fatherName = $config
    ->getFamily()
    ->getFather()
    ->getName();

```
Переменная **$fatherName** будет содержать
значение `Bob`.

* **Модуль** позволяет создавать
свойства с ограниченным
сроком действия, например, в вышеуказанном
**yaml-файле** обращение к свойству
**family.doter.age** будет зависеть
от переданной в конструктор даты:

```php
$dateList = [
    '2018-04-17',
    '2019-09-12',
    '2017-09-01'
];
foreach($dateList as $date){
    $dateTime = new DateTime($date);
    $config = new Config($dateTime);
    $doterAgeList[] = $config
        ->getFamily()
        ->getDoter()
        ->getAge();
}

```

Переменная **$doterAgeList** содержит
массив: `[1,2,0]`

* Создаваемый **модулем** php-код будет
содержать phpDoc-комментарии, соответствующие
комментариям в **yaml-файле**

## Как использовать

```php
use YamlConfig\CodeGenerator\ConfigGenerator;

$configGenerator = new ConfigGenerator();
$configGenerator
    ->setProjectPath($rootDir) // путь к папке проекта
    ->setConfigRelativePath($organizationsRelativePath) // относительный путь расположения yaml-файл с настройками
    ->setConfigCodeRelativePath($organizationsCodeRelativePath) // относительный путь к папке в которой будут сгенерирован код конфига
    ->setConfigName('Family') // название класса конфига
    ->setConfigNamespace('Config\Family') // пространство имён конфига
    ->generate(); // Генерация кода конфига
```

Особенности функции **generate**:
1) Если изменений в исходном конфиге (по сравнению со сгенерированным кодом) нет, то перегенерация не происходит.
2) В качестве необязательного параметра **generate** принимает функцию, которая будет вызвана после генерации кода.


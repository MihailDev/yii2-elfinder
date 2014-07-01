ElFinder Расширение для Yii 2
===========================

ElFinder — файловый менеджер для сайта.


## Установка

Удобнее всего установить это расширение через [composer](http://getcomposer.org/download/).

Либо запустить

```
php composer.phar require --prefer-dist mihaildev/yii2-elfinder "*"
```

или добавить

```json
"mihaildev/yii2-elfinder": "*"
```

в разделе `require` вашего composer.json файла.

## Настройка

```php
'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['@'], //глобальный доступ к фаил менеджеру @ - для авторизорованных , ? - для гостей , чтоб открыть всем ['@', '?']
            'disabledCommands' => ['netmount'], //отключение ненужных команд https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#commands
            'roots' => [
                [
                    'path' => 'files/global',
                    'name' => 'Global'
                ],
                [
                    'class' => 'mihaildev\elfinder\UserPath',
                    'path'  => 'files/user_{id}',
                    'name'  => 'My Documents'
                ],
                [
                    'path' => 'files/some',
                    'name' => ['category' => 'my','message' => 'Some Name'] //перевод Yii::t($category, $message)
                ],
                [
                    'path'   => 'files/some',
                    'name'   => ['category' => 'my','message' => 'Some Name'], // Yii::t($category, $message)
                    'access' => ['read' => '*', 'write' => 'UserFilesAccess'] // * - для всех, иначе проверка доступа в даааном примере все могут видет а редактировать могут пользователи только с правами UserFilesAccess
                ]
            ]
        ]
    ],
```

## Использование

```php
use mihaildev\elfinder\InputFile;
use mihaildev\elfinder\ElFinder;
use yii\web\JsExpression;

echo InputFile::widget([
    'language'   => 'ru',
    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter'     => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'name'       => 'myinput',
    'value'      => '',
]);

echo $form->field($model, 'attribute')->widget(InputFile::className(), [
    'language'      => 'ru',
    'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
    'options'       => ['class' => 'form-control'],
    'buttonOptions' => ['class' => 'btn btn-default'],
    'multiple'      => false       // возможность выбора нескольких файлов
]);

echo ElFinder::widget([
    'language'         => 'ru',
    'controller'       => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter'           => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'callbackFunction' => new JsExpression('function(file, id){}') // id - id виджета
]);

```

## CKEditor
```php
use mihaildev\elfinder\ElFinder;

$ckeditorOptions = ElFinder::ckeditorOptions($controller,[/* Some CKEditor Options */]);

```

Использование совместно с приложением "mihaildev/yii2-ckeditor" (https://github.com/MihailDev/yii2-ckeditor)

```php
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

$form->field($model, 'attribute')->widget(CKEditor::className(), [
  ...
  'editorOptions' => ElFinder::ckeditorOptions('elfinder',[/* Some CKEditor Options */]),
  ...
]);
```

## Полезные ссылки

ElFinder Wiki - https://github.com/Studio-42/elFinder/wiki

## Настройка

```php
'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => '*', //глобальный доступ к фаил менеджеру * - для всех
            'disabledCommands' => ['netmount'], //отключение ненужных команд https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#commands
            'roots' => [
                [
                    'path' => 'files/global',
                    'name' => 'Global'
                ],
                [
                    'class' => 'mihaildev\elfinder\UserPath',
                    'path' => 'files/user_{id}',
                    'name' => 'My Documents'
                ],
                [
                    'path' => 'files/some',
                    'name' => ['category' => 'my','message' => 'Some Name'] //перевод Yii::t($category, $message)
                ],
                [
                    'path' => 'files/some',
                    'name' => ['category' => 'my','message' => 'Some Name'] // Yii::t($category, $message)
                    'access' => ['read' => '*', 'write' => 'UserFilesAccess'] // * - для всех, иначе проверка доступа в даааном примере все могут видет а редактировать могут пользователи только с правами UserFilesAccess
                ]
            ]
        ]
    ],
```

## Использование

```php
use mihaildev/elfinder/InputFile;
use mihaildev/elfinder/Widget as ElFinder;
use \yii\web\JsExpression;

echo InputFile::widget([
    'language' => 'ru',
    'controller' => 'elfinder', //вставляем название контроллера по умолчанию равен elfinder
    'filter' => 'image', //филтер файлов, можно задать масив филтров  https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'name' => 'myinput',
    'value' => '',
]);

echo $form->field($model, 'attribute')->widget(InputFile::className(), [
  'language' => 'ru',
  'controller' => 'elfinder', //вставляем название контроллера по умолчанию равен elfinder
  'filter' => 'image', //филтер файлов, можно задать масив филтров  https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
]);

echo ElFinder::widget([
                        'language' => 'ru',
                        'controller' => 'elfinder', //вставляем название контроллера по умолчанию равен elfinder
                        'filter' => 'image', //филтер файлов, можно задать масив филтров  https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                        'callbackFunction' => new JsExpression('function(file, id){}')// id - id виджета
                      ]);

```

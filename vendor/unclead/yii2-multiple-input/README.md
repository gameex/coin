# Yii2 Multiple input widget.
Yii2 widget for handle multiple inputs for an attribute of model and tabular input for batch of models.

[![Latest Stable Version](https://poser.pugx.org/unclead/yii2-multiple-input/v/stable)](https://packagist.org/packages/unclead/yii2-multiple-input)
[![Total Downloads](https://poser.pugx.org/unclead/yii2-multiple-input/downloads)](https://packagist.org/packages/unclead/yii2-multiple-input)
[![Daily Downloads](https://poser.pugx.org/unclead/yii2-multiple-input/d/daily)](https://packagist.org/packages/unclead/yii2-multiple-input)
[![Latest Unstable Version](https://poser.pugx.org/unclead/yii2-multiple-input/v/unstable)](https://packagist.org/packages/unclead/yii2-multiple-input) 
[![License](https://poser.pugx.org/unclead/yii2-multiple-input/license)](https://packagist.org/packages/unclead/yii2-multiple-input)

## Latest release
The latest stable version of the extension is v2.14.0 Follow the [instruction](./UPGRADE.md) for upgrading from previous versions

## Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require  unclead/yii2-multiple-input "~2.0"
```

or add

```
"unclead/yii2-multiple-input": "~2.0"
```

to the require section of your `composer.json` file.

## Basic usage

![Single column example](./resources/images/single-column.gif?raw=true)

For example you want to have an ability of entering several emails of user on profile page.
In this case you can use yii2-multiple-input widget like in the following code

```php
use unclead\multipleinput\MultipleInput;

...

<?php
    echo $form->field($model, 'emails')->widget(MultipleInput::className(), [
        'max'               => 6,
        'min'               => 2, // should be at least 2 rows
        'allowEmptyList'    => false,
        'enableGuessTitle'  => true,
        'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
        'cloneButton'       => true, // show clone button
    ])
    ->label(false);
?>
```
See more in [single column](https://github.com/unclead/yii2-multiple-input/wiki/Usage#one-column)

## Advanced usage

![Multiple columns example](https://raw.githubusercontent.com/unclead/yii2-multiple-input/master/resources/images/multiple-column.gif)

For example you want to have an interface for manage user schedule. For simplicity we will store the schedule in json string.
In this case you can use yii2-multiple-input widget like in the following code

```php
use unclead\multipleinput\MultipleInput;

...

<?= $form->field($model, 'schedule')->widget(MultipleInput::className(), [
    'max' => 4,
    'columns' => [
        [
            'name'  => 'user_id',
            'type'  => 'dropDownList',
            'title' => 'User',
            'defaultValue' => 1,
            'items' => [
                1 => 'User 1',
                2 => 'User 2'
            ]
        ],
        [
            'name'  => 'day',
            'type'  => \kartik\date\DatePicker::className(),
            'title' => 'Day',
            'value' => function($data) {
                return $data['day'];
            },
            'items' => [
                '0' => 'Saturday',
                '1' => 'Monday'
            ],
            'options' => [
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true
                ]
            ]
        ],
        [
            'name'  => 'priority',
            'title' => 'Priority',
            'enableError' => true,
            'options' => [
                'class' => 'input-priority'
            ]
        ]
    ]
 ]);
?>
```
See more in [multiple columns](https://github.com/unclead/yii2-multiple-input/wiki/Usage#multiple-columns)

## Documentation

You can find a full version of documentation in [wiki](https://github.com/unclead/yii2-multiple-input/wiki/)


## License

**yii2-multiple-input** is released under the BSD 3-Clause License. See the bundled [LICENSE.md](./LICENSE.md) for details.

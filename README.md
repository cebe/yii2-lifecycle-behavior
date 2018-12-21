Yii 2 lifecycle behavior
========================

Define the lifecycle of a model by defining allowed status changes in terms of a state machine.

[![Latest Stable Version](https://poser.pugx.org/cebe/yii2-lifecycle-behavior/v/stable)](https://packagist.org/packages/cebe/yii2-lifecycle-behavior)
[![Total Downloads](https://poser.pugx.org/cebe/yii2-lifecycle-behavior/downloads)](https://packagist.org/packages/cebe/yii2-lifecycle-behavior)
[![License](https://poser.pugx.org/cebe/yii2-lifecycle-behavior/license)](https://packagist.org/packages/cebe/yii2-lifecycle-behavior)
[![Build Status](https://travis-ci.org/cebe/yii2-lifecycle-behavior.svg?branch=master)](https://travis-ci.org/cebe/yii2-lifecycle-behavior)


Installation
------------

This is an extension for the [Yii 2](http://www.yiiframework.com/) PHP framework.

Installation is recommended to be done via [composer][] by running:

	composer require cebe/yii2-lifecycle-behavior

Alternatively you can add the following to the `require` section in your `composer.json` manually
and run `composer update` afterwards:

```json
"cebe/yii2-lifecycle-behavior": "~2.0.0"
```

[composer]: https://getcomposer.org/ "The PHP package manager"


Usage
-----

You can add the behavior to an [ActiveRecord][] class. It does not work with `yii\base\Model`
because it relies on the old-attribute feature which is only available in active record.

You can add the behavior to the model by creating a `behaviors()` method if there is none yet, or
add it to the list of exising behaviors.

The following example shows how to define the allowed status changes:

```php
	public function behaviors()
	{
		return [
			'lifecycle' => [
				'class' => cebe\lifecycle\LifecycleBehavior::class,
				'validStatusChanges' => [
					'draft'     => ['ready', 'delivered'],
					'ready'     => ['draft', 'delivered'],
					'delivered' => ['payed', 'archived'],
					'payed'     => ['archived'],
					'archived'  => [],
				],
			],
		];
	}
```

The above state transitions can be visualized as the following state machine:

![Visualization of state transitions](example.png)

[ActiveRecord]: http://www.yiiframework.com/doc-2.0/guide-db-active-record.html

## Status field validation

By default, the behavior will validate the `status` attribute of the record, when `validate()` or `save()` is called
and add a validation error in case state has changed in a way that is not allowed.

- The attribute to validate can be configured by setting the `statusAttribute` property of the behavior.
- The error message can be configured by setting the `validationErrorMessage` property of the behavior.
  The place holders `{old}` and `{new}` are being replaced with the corresponding status values.

## Program flow validation

The behavior may also be used to validate status changes in program flow. This is different to user input validation as
described above, because program flow will be aborted by an [exception](src/StatusChangeNotAllowedException.php) in this case.
For user input, the recipient of the error message is the user, when status is not changed by the user,
the recipient of the error is the developer.

## Configuring different validation methods

By default status field is validated both, on validation and on update. To disable one of the methods, you may configure
the `$events` propery, which is by default:

```php
'events' => [
    BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'handleBeforeValidate',
    BaseActiveRecord::EVENT_BEFORE_UPDATE => 'handleBeforeSave',
]
```


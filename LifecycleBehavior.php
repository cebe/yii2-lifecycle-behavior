<?php
/**
 * @copyright Copyright (c) 2015 Carsten Brandt
 * @license https://github.com/cebe/yii2-lifecycle-behavior/blob/master/LICENSE
 * @link https://github.com/cebe/yii2-lifecycle-behavior#readme
 */

namespace cebe\lifecycle;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;

/**
 * Behavior that defines a models lifecycle.
 *
 * This behavior only works with ActiveRecord because it relies on the old-attribute feature
 * which is not available in `yii\base\Model`.
 * 
 * @author Carsten Brandt <mail@cebe.cc>
 */
class LifecycleBehavior extends Behavior
{
	/**
	 * @var array a set of valid status changes. The array key is the current status, the array
	 * values are array of valid stati that can be reached from the current status.
	 *
	 * If a status is not listed as array key it means that from this status any other value can be reached.
	 * If a status is final, assign it an empty array.
	 */
	public $validStatusChanges = [];
	/**
	 * @var string the model attribute that holds the status.
	 */
	public $statusAttribute = 'status';
	/**
	 * @var string the error message to add if validation fails.
	 * The place holders `{old}` and `{new}` are being replaced with the corresponding status values.
	 */
	public $validationErrorMessage = 'Invalid status change: "{old}" to "{new}"';


	/**
	 * @inheritdoc
	 */
	public function events()
	{
		return [
			'beforeValidate' => 'handleBeforeValidate',
		];
	}

	/**
	 * validate the status change
	 * @param Event $event the event that is being handled.
	 */
	public function handleBeforeValidate($event)
	{
		/** @var BaseActiveRecord $owner */
		$owner = $this->owner;
		$old = $owner->getOldAttribute($this->statusAttribute);
		$new = $owner->getAttribute($this->statusAttribute);
		if ($old != $new && isset($this->validStatusChanges[$old])) {
			if (!in_array($new, $this->validStatusChanges[$old])) {
				$params = ['new' => $new, 'old' => $old];
				$error = Yii::$app->getI18n()->format($this->validationErrorMessage, $params, Yii::$app->language);
				$owner->addError($this->statusAttribute, $error);
			}
		}
	}
}

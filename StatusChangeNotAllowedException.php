<?php
/**
 * @copyright Copyright (c) 2015 Carsten Brandt
 * @license https://github.com/cebe/yii2-lifecycle-behavior/blob/master/LICENSE
 * @link https://github.com/cebe/yii2-lifecycle-behavior#readme
 */

namespace cebe\lifecycle;

/**
 * Indicates an invalid Status change.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class StatusChangeNotAllowedException extends \Exception
{
	/**
	 * @var string old status.
	 */
	public $old;
	/**
	 * @var string new status.
	 */
	public $new;

	/**
	 * StatusChangeNotAllowedException constructor.
	 * @param string  $old old status.
	 * @param string $new new status.
	 * @param \Exception|null $previous previous exception.
	 */
	public function __construct($old, $new, $previous = null)
	{
		$this->old = $old;
		$this->new = $new;
		$message = sprintf('Invalid status change: "%s" to "%s"', $old, $new);
		parent::__construct($message, 0, $previous);
	}
}

<?php

namespace Razor;

/**
 * FluentPropertySetter makes generic a pattern that allows methods
 * to get/set through the same method. This leads to a concise API
 * without duplication but means repeating the below. As such the
 * behaviour is isolated here for spreading to other areas of the
 * framework.
 *
 * @package Razor
 */
trait FluentPropertySetter
{
	protected function setOrReturn($property, $value = null)
	{
		if ($value === null) {
			return $this->$property;
		} else {
			$this->$property = $value;
			return $this;
		}
	}
}

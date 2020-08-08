<?php

/**
 * Méthodes utiliaires sur les classes PHP
 */

namespace App;

final class PHP {
	
	/**
	 * Retourne si la classe $childClass est une sous classe de la classe $parentClass
	 * @param string $childClass
	 * @param string $parentClass
	 * @return bool
	 */
	public static function isSubclass(string $childClass, string $parentClass) : bool
	{
		if(! class_exists($childClass) OR ! class_exists($parentClass))
		{
			return FALSE;
		}
		
		$class = new \ReflectionClass($childClass);
		
		while($class = $class->getParentClass())
		{
			if($class->getName() == $parentClass)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
}
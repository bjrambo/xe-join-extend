<?php

/**
 * @class  join_extendAdminModel
 * @author BJRambo (qw5414@naver.com)
 * @brief  join_extend module's admin Model class
 **/

class join_extendAdminModel extends join_extend
{
	/**
	 * check to valid date.
	 * @param $validDate
	 * @return bool
	 */
	function checkedValidDate($validDate)
	{
		if ($validDate && $validDate < date('Ymd'))
		{
			return false;
		}

		return true;
	}
}
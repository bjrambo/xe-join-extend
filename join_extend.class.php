<?php
/**
 * @class  join_extend
 * @author 난다날아 (sinsy200@gmail.com)
 * @brief  join_extend 모듈의 상위 class
 **/

class join_extend extends ModuleObject
{
	private static $triggers = array(
		array('member.insertMember', 'join_extend', 'controller', 'triggerInsertMember', 'after'),
		array('moduleHandler.init', 'join_extend', 'controller', 'triggerModuleHandlerInit', 'after'),
		array('moduleHandler.proc', 'join_extend', 'controller', 'triggerModuleHandlerProc', 'after'),
		array('display', 'join_extend', 'controller', 'triggerDisplay', 'before'),
		array('member.deleteMember', 'join_extend', 'controller', 'triggerDeleteMember', 'before'),
	);

	/**
	 * @brief Add to module triggers when module install.
	 **/
	function moduleInstall()
	{
		$oModuleModel = &getModel('module');
		$oModuleController = getController('module');

		foreach (self::$triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}

		return new Object();
	}

	function checkUpdate()
	{
		/** @var $oDB DBMysqli */
		$oDB = &DB::getInstance();
		$oModuleModel = &getModel('module');

		foreach (self::$triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				return true;
			}
		}

		if (!$oDB->isColumnExists("join_extend_invitation", "validdate"))
		{
			return true;
		}

		return false;
	}

	function moduleUpdate()
	{
		/** @var $oDB DBMysqli */
		$oDB = &DB::getInstance();
		$oModuleModel = &getModel('module');
		$oModuleController = &getController('module');

		foreach (self::$triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}

		if (!$oDB->isColumnExists("join_extend_invitation", "validdate"))
		{
			$oDB->addColumn("join_extend_invitation", "validdate", "date");
		}

		return new Object(0, 'success_updated');
	}

	function recompileCache()
	{
	}
}

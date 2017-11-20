<?php
/**
 * @class  join_extend
 * @author 난다날아 (sinsy200@gmail.com)
 * @brief  join_extend 모듈의 상위 class
 **/

class join_extend extends ModuleObject
{
	private static $triggers = array(array('member.insertMember', 'join_extend', 'controller', 'triggerInsertMember', 'after'), array('moduleHandler.init', 'join_extend', 'controller', 'triggerModuleHandlerInit', 'after'), array('moduleHandler.proc', 'join_extend', 'controller', 'triggerModuleHandlerProc', 'after'), array('display', 'join_extend', 'controller', 'triggerDisplay', 'before'), array('member.deleteMember', 'join_extend', 'controller', 'triggerDeleteMember', 'before'),);

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

		if (empty($oModuleModel->getModuleConfig('join_extend_editor_agreement')))
		{
			return true;
		}
		if (empty($oModuleModel->getModuleConfig('join_extend_editor_private_agreement')))
		{
			return true;
		}
		if (empty($oModuleModel->getModuleConfig('join_extend_editor_private_gathering_agreement')))
		{
			return true;
		}
		if (empty($oModuleModel->getModuleConfig('join_extend_editor_welcome')))
		{
			return true;
		}
		if (empty($oModuleModel->getModuleConfig('join_extend_editor_welcome_email')))
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

		// TODO foreach to same code.
		$isDeleteCache = false;
		if ($oModuleModel->getModuleConfig('join_extend_editor_agreement'))
		{
			$args = new stdClass();
			$args->type = 'agreement';
			$args->content = $oModuleModel->getModuleConfig($args->module);
			$insertOutput = executeQuery('join_extend.insertEditorContent', $args);
			if(!$insertOutput->toBool())
			{
				return $insertOutput;
			}

			$args->module = 'join_extend_editor_agreement';
			$args->site_srl = 0;
			$output = executeQuery('module.deleteModuleConfig', $args);
			if(!$output->toBool())
			{
				return $output;
			}
			else
			{
				$isDeleteCache = true;
			}
		}
		if ($oModuleModel->getModuleConfig('join_extend_editor_private_agreement'))
		{
			$args = new stdClass();
			$args->type = 'private_agreement';
			$args->content = $oModuleModel->getModuleConfig($args->module);
			$insertOutput = executeQuery('join_extend.insertEditorContent', $args);
			if(!$insertOutput->toBool())
			{
				return $insertOutput;
			}

			$args->module = 'join_extend_editor_private_agreement';
			$args->site_srl = 0;
			if(!$output->toBool())
			{
				return $output;
			}
			else
			{
				$isDeleteCache = true;
			}
		}
		if ($oModuleModel->getModuleConfig('join_extend_editor_private_gathering_agreement'))
		{
			$args = new stdClass();
			$args->type = 'private_gathering_agreement';
			$args->content = $oModuleModel->getModuleConfig($args->module);
			$insertOutput = executeQuery('join_extend.insertEditorContent', $args);
			if(!$insertOutput->toBool())
			{
				return $insertOutput;
			}

			$args->module = 'join_extend_editor_private_gathering_agreement';
			$args->site_srl = 0;
			$output = executeQuery('module.deleteModuleConfig', $args);
			if(!$output->toBool())
			{
				return $output;
			}
			else
			{
				$isDeleteCache = true;
			}
		}
		if ($oModuleModel->getModuleConfig('join_extend_editor_welcome'))
		{
			$args = new stdClass();
			$args->type = 'welcome';
			$args->content = $oModuleModel->getModuleConfig($args->module);
			$insertOutput = executeQuery('join_extend.insertEditorContent', $args);
			if(!$insertOutput->toBool())
			{
				return $insertOutput;
			}

			$args->module = 'join_extend_editor_welcome';
			$args->site_srl = 0;
			$output = executeQuery('module.deleteModuleConfig', $args);
			if(!$output->toBool())
			{
				return $output;
			}
			else
			{
				$isDeleteCache = true;
			}
		}
		if ($oModuleModel->getModuleConfig('join_extend_editor_welcome_email'))
		{
			$args = new stdClass();
			$args->type = 'welcome_email';
			$args->content = $oModuleModel->getModuleConfig($args->module);
			$insertOutput = executeQuery('join_extend.insertEditorContent', $args);
			if(!$insertOutput->toBool())
			{
				return $insertOutput;
			}

			$args->module = 'join_extend_editor_welcome_email';
			$args->site_srl = 0;
			$output = executeQuery('module.deleteModuleConfig', $args);
			if(!$output->toBool())
			{
				return $output;
			}
			else
			{
				$isDeleteCache = true;
			}
		}

		if($isDeleteCache)
		{
			$oCacheHandler = CacheHandler::getInstance('object', NULL, TRUE);
			if($oCacheHandler->isSupport())
			{
				$oCacheHandler->invalidateGroupKey('site_and_module');
			}
		}

		return new Object(0, 'success_updated');
	}

	function recompileCache()
	{
	}
}

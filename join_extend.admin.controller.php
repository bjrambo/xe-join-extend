<?php
/**
 * @class  join_extendAdminController
 * @author @sinsy200 (sinsy200@gmail.com)
 * @brief  member_join_extend module's admin controller class
 **/

class join_extendAdminController extends join_extend
{

	/**
	 * @brief init
	 **/
	function init()
	{
	}

	/**
	 * @brief save to module setting
	 **/
	function procJoin_extendAdminInsertConfig()
	{
		/** @var $oJoinExtendModel join_extendModel */
		$oJoinExtendModel = getModel('join_extend');
		$oModuleController = getController('module');

		$obj = Context::getRequestVars();

		$config = $oJoinExtendModel->getConfig();
		$config_act = $obj->config_act;
		unset($obj->config_act);

		if (isset($obj->user_name_type))
		{
			$config_list = get_object_vars($config);
			if (count($config_list))
			{
				foreach ($config_list as $var_name => $val)
				{
					if (strpos($var_name, '_required') || strpos($var_name, '_no_mod') || strpos($var_name, '_lower_length') || strpos($var_name, '_upper_length') || strpos($var_name, '_type'))
					{
						unset($config->{$var_name});
					}
				}
			}
		}

		if ($config_act == "dispJoin_extendAdminAfterConfig")
		{
			if (!$obj->welcome_title)
			{
				$obj->welcome_title = '';
			}
			if (!$obj->welcome_email_title)
			{
				$obj->welcome_email_title = '';
			}
			if (!$obj->notify_admin_collect_number)
			{
				$obj->notify_admin_collect_number = '';
			}
		}
		if ($config_act == "dispJoin_extendAdminCouponConfig")
		{
			if (!$obj->coupon_var_name)
			{
				$obj->coupon_var_name = '';
			}
		}
		if ($config_act == "dispJoin_extendAdminExtendVarConfig")
		{
			if (!$obj->sex_var_name)
			{
				$obj->sex_var_name = '';
			}
			if (!$obj->man_value)
			{
				$obj->man_value = '';
			}
			if (!$obj->woman_value)
			{
				$obj->woman_value = '';
			}
			if (!$obj->age_var_name)
			{
				$obj->age_var_name = '';
			}
			if (!$obj->recoid_var_name)
			{
				$obj->recoid_var_name = '';
			}
			if (!$obj->recoid_point)
			{
				$obj->recoid_point = '';
			}
			if (!$obj->joinid_point)
			{
				$obj->joinid_point = '';
			}
		}
		if ($config_act == "dispJoin_extendAdminIndex")
		{
			if (!$obj->admin_id)
			{
				$obj->admin_id = '';
			}
		}
		if ($config_act == "dispJoin_extendAdminInputConfig")
		{
			$config_list = get_object_vars($obj);
			if (count($config_list))
			{
				foreach ($config_list as $var_name => $val)
				{
					if (strpos($var_name, "_type"))
					{
						$name = substr($var_name, 0, -5);
						if (!$obj->{$name . "_lower_length"})
						{
							$obj->{$name . "_lower_length"} = '';
						}
						if (!$obj->{$name . "_upper_length"})
						{
							$obj->{$name . "_upper_length"} = '';
						}
					}
				}
			}
		}
		if ($config_act == "dispJoin_extendAdminRestrictionsConfig")
		{
			if (!$obj->age_restrictions)
			{
				$obj->age_restrictions = '';
			}
			if (!$obj->age_upper_restrictions)
			{
				$obj->age_upper_restrictions = '';
			}
			if (!$obj->msg_junior_join)
			{
				$obj->msg_junior_join = '';
			}
		}

		// Merge to old config when set new config.
		$config_list = get_object_vars($obj);
		if (count($config_list))
		{
			foreach ($config_list as $var_name => $val)
			{
				$config->{$var_name} = $val;
			}
		}

		$output = $oModuleController->insertModuleConfig('join_extend', $config);
		if(!$output->toBool())
		{
			return $output;
		}

		$this->setMessage('success');

		if (Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			if($config_act)
			{
				$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', $config_act));
			}
		}
	}

	function procJoin_extendAdminGenerateInvitation()
	{
		/** @var  $oJoinExtendAdminModel join_extendAdminModel */
		$oJoinExtendAdminModel = getAdminModel('join_extend');
		$count = intval(Context::get('count'));
		if ($count < 1 || $count > 100)
		{
			return new Object(-1, 'msg_invitation_incorrect_count');
		}

		$validDate = Context::get('validdate');
		if (!$oJoinExtendAdminModel->checkedValidDate($validDate))
		{
			return new Object(-1, 'msg_validdate_past');
		}

		$oDB = &DB::getInstance();
		$oDB->begin();
		for ($i = 0; $i < $count; $i++)
		{
			$args = new stdClass();
			while (1)
			{
				$args->invitation_code = strtoupper(md5(microtime() + $i));
				$output = $oDB->executeQuery('join_extend.getInvitation', $args);
				if (!$output->toBool())
				{
					$oDB->rollback();
					return $output;
				}
				if (!$output->data)
				{
					break;
				}
			}

			$args->invitation_srl = getNextSequence();
			$args->own_member_srl = 0;
			$args->validdate = $validDate;
			$output = $oDB->executeQuery('join_extend.insertInvitation', $args);
			if (!$output->toBool())
			{
				$oDB->rollback();
				return $output;
			}
		}
		$oDB->commit();

		$this->setMessage('success_update');

		if (Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispJoin_extendAdminInvitationConfig'));
		}
	}

	function procJoin_extendAdminDeleteInvitation()
	{
		$args = new stdClass();
		$args->invitation_srls = Context::get('invitation_srls');
		if (!$args->invitation_srls)
		{
			return new Object(-1, 'invitaion_srls is missing');
		}

		$output = executeQuery('join_extend.deleteInvitation', $args);
		if (!$output->toBool())
		{
			return $output;
		}

		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispJoin_extendAdminInvitationConfig'));
	}

	function procJoin_extendAdminGenerateCoupon()
	{
		/** @var $oJoinExtendAdminModel join_extendAdminModel */
		$oJoinExtendAdminModel = getAdminModel('join_extend');

		$count = intval(Context::get('count'));
		if ($count < 1 || $count > 100)
		{
			return new Object(-1, 'msg_invitation_incorrect_count');
		}

		$point = Context::get('point');
		if (!$point || !is_numeric($point) || intVal($point) < 0)
		{
			return new Object(-1, 'msg_invalid_number');
		}

		$validDate = Context::get('validdate');
		if (!$oJoinExtendAdminModel->checkedValidDate($validDate))
		{
			return new Object(-1, 'msg_validdate_past');
		}

		$oDB = &DB::getInstance();
		$oDB->begin();
		for ($i = 0; $i < $count; $i++)
		{
			$args = new stdClass();
			while (1)
			{
				$args->coupon_code = strtoupper(md5(microtime() + $i));
				$output = $oDB->executeQuery('join_extend.getCoupon', $args);
				if (!$output->toBool())
				{
					$oDB->rollback();
					return $output;
				}
				if (!$output->data)
				{
					break;
				}
			}

			$args->coupon_srl = getNextSequence();
			$args->own_member_srl = 0;
			$args->validdate = $validDate;
			$args->point = $point;
			$output = $oDB->executeQuery('join_extend.insertCoupon', $args);
			if (!$output->toBool())
			{
				$oDB->rollback();
				return $output;
			}
		}

		$oDB->commit();

		if (Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispJoin_extendAdminCouponConfig'));
		}
	}

	function procJoin_extendAdminDeleteCoupon()
	{
		$args = new stdClass();
		$args->coupon_srls = Context::get('coupon_srls');
		if (!$args->coupon_srls)
		{
			return new Object(-1, 'coupon_srls is missing');
		}

		$output = executeQuery('join_extend.deleteCoupon', $args);
		if (!$output->toBool())
		{
			return $output;
		}

		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispJoin_extendAdminCouponConfig'));
	}
}

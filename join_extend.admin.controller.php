<?php
/**
 * @class  join_extendAdminController
 * @author 난다날아 (sinsy200@gmail.com)
 * @brief  member_join_extend모듈의 admin controller class
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

		// TODO $config_act to display action name.
		if ($config_act == "after_config")
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
		if ($config_act == "coupon_config")
		{
			if (!$obj->coupon_var_name)
			{
				$obj->coupon_var_name = '';
			}
		}
		if ($config_act == "extend_var_config")
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
		if ($config_act == "index")
		{
			if (!$obj->admin_id)
			{
				$obj->admin_id = '';
			}
		}
		if ($config_act == "input_config")
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
		if ($config_act == "restrictions_config")
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
			else
			{
				$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispJoin_extendAdminIndex'));
			}
		}
	}

	/**
	 * @brief 에디터 설정 저장
	 **/
	function procJoin_extendAdminInsertEditor()
	{
		$type = Context::get('type');
		$content = new stdClass();
		switch ($type)
		{
			case 'agreement':
				$content->agreement = Context::get('agreement');
				break;
			case 'private_agreement':
				$content->private_agreement = Context::get('private_agreement');
				break;
			case 'private_gathering_agreement':
				$content->private_gathering_agreement = Context::get('private_gathering_agreement');
				break;
			case 'welcome':
				$content->welcome = Context::get('welcome');
				break;
			case 'welcome_email':
				$content->welcome_email = Context::get('welcome_email');
				break;
		}

		// module Controller 객체 생성하여 입력
		$oModuleController = getController('module');
		$output = $oModuleController->insertModuleConfig('join_extend_editor_' . $type, $content);

		if (Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispJoin_extendAdminAgreeConfig'));
		}
	}

	/**
	 * @brief 주민등록번호를 새 테이블로 이동
	 **/
	function procJoin_extendAdminUpdateTable()
	{
		$oDB = &DB::getInstance();
		$count = Context::get('count');
		$start_idx = Context::get('start_idx');

		$args->order_type = 'asc';
		$args->list_count = $count;
		$args->page = $start_idx;
		$output = $oDB->executeQuery('join_extend.getOldJumin', $args);
		if (!$output->toBool())
		{
			return $output;
		}

		if ($output->data && count($output->data))
		{
			foreach ($output->data as $val)
			{
				$output2 = $oDB->executeQuery('join_extend.insertJuminToNewTable', $val);
				if (!$output2->toBool())
				{
					return $output2;
				}
			}
		}

		$percent = $output->page_navigation->cur_page / $output->page_navigation->total_page;

		$this->add('next_idx', intVal($start_idx) + 1);
		$this->add('percent', $percent);
		if ($percent == 1)
		{
			$oDB->dropColumn('member', 'jumin');
			$this->setMessage('complete_update_table');
		}
	}

	/**
	 * @brief 에디터 이전
	 **/
	function updateEditor()
	{
		// 기존 설정을 가져온다.
		$oJoinExtendModel = &getModel('join_extend');
		$config = $oJoinExtendModel->_getConfig(false, false);

		// 에디터는 별도 저장
		$oModuleController = &getController('module');
		$output = $oModuleController->insertModuleConfig('join_extend_editor_agreement', $config->agreement);
		if (!$output->toBool())
		{
			return $output;
		}
		$output = $oModuleController->insertModuleConfig('join_extend_editor_private_agreement', $config->private_agreement);
		if (!$output->toBool())
		{
			return $output;
		}
		$output = $oModuleController->insertModuleConfig('join_extend_editor_private_gathering_agreement', $config->private_gathering_agreement);
		if (!$output->toBool())
		{
			return $output;
		}
		$output = $oModuleController->insertModuleConfig('join_extend_editor_welcome', $config->welcome);
		if (!$output->toBool())
		{
			return $output;
		}

		// 기존 설정에서 에디터 내용은 삭제
		unset($config->agreement);
		unset($config->private_agreement);
		unset($config->private_gathering_agreement);
		unset($config->welcome);
		$output = $oModuleController->insertModuleConfig('join_extend', $config);

		return $output;
	}

	/**
	 * @brief 초대장 생성
	 **/
	function procJoin_extendAdminGenerateInvitation()
	{
		// 개수 확인
		$count = intVal(Context::get('count'));
		if ($count < 1 || $count > 100)
		{
			$this->SetError(1);
			$this->SetMessage('msg_invitation_incorrect_count');
			return;
		}

		// 유효기간 확인
		$validdate = Context::get('validdate');
		if ($validdate && $validdate < date("Ymd"))
		{
			$this->SetError(1);
			$this->SetMessage('msg_validdate_past');
			return;
		}

		// 초대장 생성
		$oDB = &DB::getInstance();
		$oDB->begin();
		for ($i = 0; $i < $count; $i++)
		{

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
			$args->validdate = $validdate;
			$output = $oDB->executeQuery('join_extend.insertInvitation', $args);
			if (!$output->toBool())
			{
				$oDB->rollback();
				return $output;
			}
		}
		$oDB->commit();
	}

	/**
	 * @brief 초대장 삭제
	 **/
	function procJoin_extendAdminDeleteInvitation()
	{
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

		$this->setMessage('success_deleted');
	}

	/**
	 * @brief 쿠폰 생성
	 **/
	function procJoin_extendAdminGenerateCoupon()
	{
		// 개수 확인
		$count = intVal(Context::get('count'));
		if ($count < 1 || $count > 100)
		{
			$this->SetError(1);
			$this->SetMessage('msg_invitation_incorrect_count');
			return;
		}

		// 포인트 확인
		$point = Context::get('point');
		if (!$point || !is_numeric($point) || intVal($point) < 0)
		{
			$this->SetError(1);
			$this->SetMessage('msg_invalid_number');
			return;
		}

		// 유효기간 확인
		$validdate = Context::get('validdate');
		if ($validdate && $validdate < date("Ymd"))
		{
			$this->SetError(1);
			$this->SetMessage('msg_validdate_past');
			return;
		}

		// 쿠폰 생성
		$oDB = &DB::getInstance();
		$oDB->begin();
		for ($i = 0; $i < $count; $i++)
		{

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
			$args->validdate = $validdate;
			$args->point = $point;
			$output = $oDB->executeQuery('join_extend.insertCoupon', $args);
			if (!$output->toBool())
			{
				$oDB->rollback();
				return $output;
			}
		}
		$oDB->commit();
	}

	/**
	 * @brief 쿠폰 삭제
	 **/
	function procJoin_extendAdminDeleteCoupon()
	{
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

		$this->setMessage('success_deleted');
	}
}

?>

<?php
/**
 * @class  join_extendAdminView
 * @author sinsy200 (sinsy200@gmail.com)
 * @brief  join_extend module's admin view class
 **/

class join_extendAdminView extends join_extend
{
	function init()
	{
	}

	/**
	 * @brief Display to module index.
	 **/
	function dispJoin_extendAdminIndex()
	{
		/** @var $oJoinExtendModel join_extendModel */
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$oModuleModel = getModel('module');
		$skin_list = $oModuleModel->getSkins($this->module_path);
		Context::set('skin_list', $skin_list);

		$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
		Context::set('mskin_list', $mskin_list);

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('index');
	}

	/**
	 * @brief Display to agree config.
	 **/
	function dispJoin_extendAdminAgreeConfig()
	{
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$oEditorModel = getModel('editor');

		$option = new stdClass();
		$option->primary_key_name = 'site_srl';
		$option->allow_html = 'Y';
		$option->allow_fileupload = false;
		$option->enable_autosave = false;
		$option->enable_default_component = true;
		$option->enable_component = false;
		$option->resizable = false;
		$option->disable_html = false;
		$option->height = 200;
		$option->editor_toolbar = 'default';
		$option->editor_toolbar_hide = 'N';

		// agreement editor
		$option->content_key_name = 'agreement';
		$editor_agreement = $oEditorModel->getEditor(0, $option);
		Context::set('editor_agreement', $editor_agreement);

		// private_agreement editor
		$option->content_key_name = 'private_agreement';
		$editor_private_agreement = $oEditorModel->getEditor(0, $option);
		Context::set('editor_private_agreement', $editor_private_agreement);

		// private_gathering_agreement editor
		$option->content_key_name = 'private_gathering_agreement';
		$editor_private_gathering_agreement = $oEditorModel->getEditor(0, $option);
		Context::set('editor_private_gathering_agreement', $editor_private_gathering_agreement);

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('agree_config');
	}

	function dispJoin_extendAdminExtendVarConfig()
	{
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('extend_var_config');
	}

	function dispJoin_extendAdminRestrictionsConfig()
	{
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('restrictions_config');
	}

	function dispJoin_extendAdminAfterConfig()
	{
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$oEditorModel = getModel('editor');
		// TODO: javascript error when load two editor.
		$option = new stdClass();
		$option->primary_key_name = 'site_srl';
		$option->allow_html = 'Y';
		$option->allow_fileupload = false;
		$option->enable_autosave = false;
		$option->enable_default_component = true;
		$option->enable_component = false;
		$option->resizable = false;
		$option->disable_html = false;
		$option->height = 300;
		$option->editor_toolbar = 'default';
		$option->editor_toolbar_hide = 'N';

		$option->content_key_name = 'welcome';
		$editor_welcome = $oEditorModel->getEditor(0, $option);
		Context::set('editor_welcome', $editor_welcome);

		$option->content_key_name = 'welcome_email';
		$editor_welcome_email = $oEditorModel->getEditor(0, $option);
		Context::set('editor_welcome_email', $editor_welcome_email);

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('after_config');
	}

	function dispJoin_extendAdminJuminConfig()
	{
		$oJoinExtendModel = &getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('jumin_config');
	}

	function dispJoin_extendAdminInputConfig()
	{
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$oMemberModel = getModel('member');
		$extend_list = $oMemberModel->getJoinFormList();
		if (!count($extend_list))
		{
			$extend_list = array();
		}
		Context::set('extend_list', $extend_list);

		$msg_user_id_length = Context::getLang('msg_user_id_length');
		$msg_user_name_length = Context::getLang('msg_user_name_length');
		$msg_nick_name_length = Context::getLang('msg_nick_name_length');
		$msg_email_length = Context::getLang('msg_email_length');
		Context::addHtmlHeader(sprintf('<script type="text/javascript"> var msg_user_id_length="%s"; var msg_user_name_length="%s"; var msg_nick_name_length="%s"; var msg_email_length="%s"; </script>', $msg_user_id_length, $msg_user_name_length, $msg_nick_name_length, $msg_email_length));

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('input_config');
	}

	function getJoin_extendAdminColorset()
	{
		$skin = Context::get('skin');
		$type = Context::get('type');
		if ($type == 'mobile')
		{
			$mskin = Context::get('mskin');
		}

		$oModuleModel = getModel('module');
		$config = $oModuleModel->getModuleConfig('join_extend');
		if ($type == 'pc')
		{
			$skin_info = $oModuleModel->loadSkinInfo($this->module_path, $skin);
			if (!$config->colorset)
			{
				$config->colorset = "white";
			}
			Context::set('skin_info', $skin_info);
			$teplatefile = 'colorset_list';
		}
		else
		{
			$skin_info = $oModuleModel->loadSkinInfo($this->module_path, $mskin, "m.skins");
			if (!$config->mcolorset)
			{
				$config->mcolorset = "white";
			}
			Context::set('mskin_info', $skin_info);
			$teplatefile = 'mcolorset_list';
		}

		Context::set('type', $type);

		Context::set('config', $config);

		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path . 'tpl', $teplatefile);

		$this->add('tpl', $tpl);
	}

	function dispJoin_extendAdminInvitationConfig()
	{
		$oMemberModel = getModel('member');
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$args = new stdClass();
		$args->page = Context::get('page');
		$args->invitation_code = Context::get('code');
		$args->joindate = Context::get('joindate');
		$output = executeQuery('join_extend.getInvitationList', $args);
		if (!$output->toBool())
		{
			return $output;
		}

		if ($output->data)
		{
			foreach ($output->data as $no => $val)
			{
				if ($val->member_srl)
				{
					$member_info = $oMemberModel->getMemberInfoByMemberSrl($val->member_srl);
					if ($member_info)
					{
						$val->join_id = $member_info->user_id;
					}
					else
					{
						$val->join_id = Context::getLang('deleted_member');
					}
				}
				if ($val->joindate == "-")
				{
					$val->joindate = 0;
				}
				$val->code = substr($val->code, 0, 8) . '-' . substr($val->code, 8, 8) . '-' . substr($val->code, 16, 8) . '-' . substr($val->code, 24, 8);
			}
		}

		Context::set('invitation_list', $output->data);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);

		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('invitation_config');
	}

	//TODO: get to member signup config for member extra vars.
	function dispJoin_extendAdminCouponConfig()
	{
		$oMemberModel = getModel('member');
		$oJoinExtendModel = getModel('join_extend');
		$config = $oJoinExtendModel->getConfig();
		Context::set('config', $config);

		$args = new stdClass();
		$args->page = Context::get('page');
		$args->invitation_code = Context::get('code');
		$args->joindate = Context::get('joindate');
		$output = executeQuery('join_extend.getCouponList', $args);
		if (!$output->toBool())
		{
			return $output;
		}

		if ($output->data)
		{
			foreach ($output->data as $no => $val)
			{
				if ($val->member_srl)
				{
					$member_info = $oMemberModel->getMemberInfoByMemberSrl($val->member_srl);
					if ($member_info)
					{
						$val->join_id = $member_info->user_id;
					}
					else
					{
						$val->join_id = Context::getLang('deleted_member');
					}
				}
				if ($val->joindate == "-")
				{
					$val->joindate = 0;
				}
				$val->code = substr($val->code, 0, 8) . '-' . substr($val->code, 8, 8) . '-' . substr($val->code, 16, 8) . '-' . substr($val->code, 24, 8);
			}
		}

		Context::set('coupon_list', $output->data);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 지정
		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile('coupon_config');
	}
}

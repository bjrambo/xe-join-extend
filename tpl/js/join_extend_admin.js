function doGetSkinColorset(skin) {
	var params = {
		skin: skin,
		type : 'pc'
	};

	var response_tags = ['error', 'message', 'tpl'];
	exec_xml('join_extend', 'getJoin_extendAdminColorset', params, function (ret) {
		var $colorset = jQuery('#skin_colorset'), old_h, new_h;

		old_h = $colorset.height();
		$colorset.html(ret.tpl);
		new_h = $colorset.height();

		if (typeof(fixAdminLayoutFooter) == "function") fixAdminLayoutFooter(new_h - old_h);
	}, response_tags);
}

function doGetMSkinColorset(mskin) {
	var params = {
		mskin: mskin,
		type : 'mobile'
	};

	var response_tags = ['error', 'message', 'tpl'];
	exec_xml('join_extend', 'getJoin_extendAdminColorset', params, function(ret){
		var $colorset = jQuery('#mskin_colorset'), old_h, new_h;

		old_h = $colorset.height();
		$colorset.html(ret.tpl);
		new_h = $colorset.height();

		if (typeof(fixAdminLayoutFooter) == "function") fixAdminLayoutFooter(new_h - old_h);
	}, response_tags);
}

// 초대장 개별 삭제
function doDeleteInvitation(invitation_srl) {
	var fo_obj = xGetElementById("fo_invitation");
	if (!fo_obj) return;
	fo_obj.invitation_srls.value = invitation_srl;
	procFilter(fo_obj, delete_invitation);
}

// 초대장 일괄 삭제
function doDeleteInvitations() {
	var fo_obj = xGetElementById("invitation_list");
	var invitation_srl = new Array();

	if (typeof(fo_obj.cart.length) == 'undefined') {
		if (fo_obj.cart.checked) invitation_srl[invitation_srl.length] = fo_obj.cart.value;
	} else {
		var length = fo_obj.cart.length;
		for (var i = 0; i < length; i++) {
			if (fo_obj.cart[i].checked) invitation_srl[invitation_srl.length] = fo_obj.cart[i].value;
		}
	}

	if (invitation_srl.length < 1) return;

	var fo_form = xGetElementById("fo_invitation");
	fo_form.invitation_srls.value = invitation_srl.join(',');
	procFilter(fo_form, delete_invitation);
}

// 초대장 삭제 후
function completeDeleteInvitation(ret_obj) {
	alert(ret_obj['message']);
	location.href = current_url;
}

// 쿠폰 개별 삭제
function doDeleteCoupon(coupon_srl) {
	var fo_obj = xGetElementById("fo_coupon");
	if (!fo_obj) return;
	fo_obj.coupon_srls.value = coupon_srl;
	procFilter(fo_obj, delete_coupon);
}

// 쿠폰 일괄 삭제
function doDeleteCoupons() {
	var fo_obj = xGetElementById("coupon_list");
	var coupon_srl = new Array();

	if (typeof(fo_obj.cart.length) == 'undefined') {
		if (fo_obj.cart.checked) coupon_srl[coupon_srl.length] = fo_obj.cart.value;
	} else {
		var length = fo_obj.cart.length;
		for (var i = 0; i < length; i++) {
			if (fo_obj.cart[i].checked) coupon_srl[coupon_srl.length] = fo_obj.cart[i].value;
		}
	}

	if (coupon_srl.length < 1) return;

	var fo_form = xGetElementById("fo_coupon");
	fo_form.coupon_srls.value = coupon_srl.join(',');
	procFilter(fo_form, delete_coupon);
}

// 쿠폰 삭제 후
function completeDeleteCoupon(ret_obj) {
	alert(ret_obj['message']);
	location.href = current_url;
}

// 정보입력 설정 검사 (XE의 기본 필터 길이제안 안에서 조절할 수 있다)
function doCheckInputConfig(obj) {
	var lower_length = 0;
	var upper_length = 0;

	// 아이디 길이 검사
	lower_length = obj.user_id_lower_length.value ? obj.user_id_lower_length.value : 3;
	upper_length = obj.user_id_upper_length.value ? obj.user_id_upper_length.value : 20;
	if (lower_length < 3 || upper_length > 20) {
		alert(msg_user_id_length);
		return false;
	}

	// 이름 길이 검사
	lower_length = obj.user_name_lower_length.value ? obj.user_name_lower_length.value : 2;
	upper_length = obj.user_name_upper_length.value ? obj.user_name_upper_length.value : 40;
	if (lower_length < 2 || upper_length > 40) {
		alert(msg_user_name_length);
		return false;
	}

	// 닉네임 길이 검사
	lower_length = obj.nick_name_lower_length.value ? obj.nick_name_lower_length.value : 2;
	upper_length = obj.nick_name_upper_length.value ? obj.nick_name_upper_length.value : 40;
	if (lower_length < 2 || upper_length > 40) {
		alert(msg_nick_name_length);
		return false;
	}

	// 이메일 길이 검사
	lower_length = obj.email_address_lower_length.value ? obj.email_address_lower_length.value : 1;
	upper_length = obj.email_address_upper_length.value ? obj.email_address_upper_length.value : 200;
	if (lower_length < 1 || upper_length > 200) {
		alert(msg_email_length);
		return false;
	}

	return procFilter(obj, insert_config)
}
(function(webim, ajaxurl, path){
	webim.setting.defaults.data = {
		play_sound: true,
		minimize_layout: true,
		buddy_sticky: true
	};
	webim.defaults.urls = {
		online: ajaxurl + "?action=webim_online",
		offline: ajaxurl + "?action=webim_offline",
		message: ajaxurl + "?action=webim_message",
		presence: ajaxurl + "?action=webim_presence",
		refresh: ajaxurl + "?action=webim_refresh",
		status: ajaxurl + "?action=webim_status"
	};
	webim.setting.defaults.url = ajaxurl + "?action=webim_";
	webim.history.defaults.urls = {
		load: ajaxurl + "?action=webim_history",
		clear: ajaxurl + "?action=clear_history"
	};
	webim.room.defaults.urls = {
		member: ajaxurl + "?action=webim_members",
		join: ajaxurl + "?action=webim_join",
		leave: ajaxurl + "?action=webim_leave"
	};
	webim.buddy.defaults.url = ajaxurl + "?action=webim_buddies";

	webim.ui.emot.init({"dir": path + "static/images/emot/default"});
	var soundUrls = {
		lib: path + "static/assets/sound.swf",
		msg: path + "static/assets/sound/msg.mp3"
	};
	var ui = new webim.ui(document.body, {
		soundUrls: soundUrls,
		layoutOptions: {
			unscalable: true
		}
	}), im = ui.im;
	ui.addApp("buddy", {
		title: webim.ui.i18n("online support"),
		disable_user: true,
		disable_group: true
	});
	ui.addApp("visitorstatus");
	ui.render();
	im.autoOnline() && im.online();

})(window.webim, _webim_ajaxurl, _webim_path);

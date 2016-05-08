/*
 * Copyright (c) Andr3as
 * as-is and without warranty under the MIT License.
 * See http://opensource.org/licenses/MIT for more information. 
 * This information must remain intact.
 */

(function(global, $){
	
	var codiad = global.codiad,
		scripts = document.getElementsByTagName('script'),
		path = scripts[scripts.length-1].src.split('?')[0],
		curpath = path.split('/').slice(0, -1).join('/')+'/';

	$(function() {
		codiad.TwoFactor.init();
	});

	codiad.TwoFactor = {
		
		loginForm: $('#login'),
		path: curpath,
		
		init: function() {
			var _this = this;
			this.loginForm.on('submit', function(e) {
				e.preventDefault();
				_this.authenticate();
			});
		},
		
		authenticate: function() {
			var _this = this;
			$.post(this.path + 'controller.php?action=authenticate', this.loginForm.serialize(), function(data) {
				data = JSON.parse(data);
				if (data.status == "tfa") {
					$('.username').hide();
					$('.password').hide();
					$('.token').show();
					codiad.message.notice(data.message);
				} else if (data.status == "success") {
					var origin = _this.path.substring(0, _this.path.indexOf("/plugins"));
					document.location.href = origin;
				} else {
					codiad.message.error(data.message);
				}
			});
		},
	};
})(this, jQuery);
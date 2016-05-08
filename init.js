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
		
		path: curpath,
		
		init: function() {
			var _this = this;
			$( document ).on("click", "#tfa_form .button", function() {
				_this.handleForm();
			});
		},
		
		handleForm: function() {
			var _this = this;
			$.post(this.path + 'controller.php?action=switchState', {token: $('#tfa_form .token').val(), password: $('#tfa_form .password').val()}, function(data) {
				data = JSON.parse(data);
				codiad.message[data.status](data.message);
				if (data.status == "success") {
					codiad.modal.unload();
					codiad.settings.show(_this.path + 'dialog.php');
				}
			});
		},
		
		sidebar: function() {
			codiad.modal.load(this.path + "dialog.php");
		}
	};
})(this, jQuery);
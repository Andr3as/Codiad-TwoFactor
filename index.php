<!doctype html>

<?php
	require_once('../../common.php');
?>

<head>
    <meta charset="utf-8">
    <title><?php i18n("CODIAD"); ?></title>
    <link rel="stylesheet" href="../../themes/default/jquery.toastmessage.css">
    <link rel="stylesheet" href="../../themes/default/reset.css">
    <link rel="stylesheet" href="../../themes/default/fonts.css">
    <link rel="stylesheet" href="../../themes/default/screen.css">
    <link rel="icon" href="../../favicon.ico" type="image/x-icon" />
</head>

<body>
    <script>
    var i18n = (function(lang) {
        return function(word,args) {
            var x;
            var returnw = (word in lang) ? lang[word] : word;
            for(x in args){
                returnw=returnw.replace("%{"+x+"}%",args[x]);   
            }
            return returnw;
        }
    })(<?php echo json_encode($lang); ?>)
    </script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>!window.jQuery && document.write(unescape('%3Cscript src="js/jquery-1.7.2.min.js"%3E%3C/script%3E'));</script>
    <script src="../../js/jquery-ui-1.8.23.custom.min.js"></script>
    <script src="../../js/jquery.css3.min.js"></script>
    <script src="../../js/jquery.easing.js"></script>
    <script src="../../js/jquery.toastmessage.js"></script>
    <script src="../../js/amplify.min.js"></script>
    <script src="../../js/localstorage.js"></script>
    <script src="../../js/jquery.hoverIntent.min.js"></script>
    <script src="../../js/system.js"></script>
    <script src="../../js/sidebars.js"></script>
    <script src="../../js/modal.js"></script>
    <script src="../../js/message.js"></script>
    <script src="../../js/jsend.js"></script>
    <script src="../../js/instance.js?v=<?php echo time(); ?>"></script>
    <div id="message"></div>

    <form id="login" method="post" style="position: fixed; width: 350px; top: 30%; left: 50%; margin-left: -175px; padding: 35px;">

        <div class="username">
        	<label><span class="icon-user login-icon"></span> <?php i18n("Username"); ?></label>
        	<input type="text" name="username" autofocus="autofocus" autocomplete="off">
        </div>

        <div class="password">
        	<label><span class="icon-lock login-icon"></span> <?php i18n("Password"); ?></label>
        	<input type="password" name="password">
        </div>

        <div class="token" style="display: none;">
        	<label><span class="icon-lock login-icon"></span> 2FA <?php i18n("Token"); ?></label>
        	<input type="number" name="token">
        </div>

        <button>Login</button>

    </form>

    <script src="login.js"></script>

</body>
</html>

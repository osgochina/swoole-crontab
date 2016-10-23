<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

    <title> SmartAdmin </title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Use the correct meta names below for your web application
         Ref: http://davidbcalhoun.com/2010/viewport-metatag

    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">-->

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Basic Styles -->
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/font-awesome.min.css">

    <!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/smartadmin-production.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/smartadmin-skins.css">

    <!-- SmartAdmin RTL Support is under construction
        <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.css"> -->

    <!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/demo.css">

    <!-- FAVICONS -->
    <link rel="shortcut icon" href="/static/smartadmin/img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/static/smartadmin/img/favicon/favicon.ico" type="image/x-icon">
</head>
<body id="login" class="animated fadeInDown">
<!-- possible classes: minified, no-right-panel, fixed-ribbon, fixed-header, fixed-width-->
<header id="header">
    <!--<span id="logo"></span>-->

    <div id="logo-group">
        <span id="logo"> <img src="/static/smartadmin/img/logo.png" alt="SmartAdmin"> </span>
    </div>
</header>

<div id="main" role="main">
    <!-- MAIN CONTENT -->
    <div id="content" class="container">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4  col-md-offset-4">
                <div class="well no-padding">
                    <form method="post" id="login-form" class="smart-form client-form">
                        <header>
                            用户登陆
                        </header>

                        <fieldset>
                            <?php if(isset($message)){ ?>
                            <section>
                                <div class="alert alert-danger fade in">
                                    <button class="close" data-dismiss="alert">×</button>
                                    <i class="fa-fw fa fa-times"></i>
                                    <strong>Error!</strong> <?=$message?>
                                </div>
                            </section>
                            <?php } ?>
                            <section>
                                <label class="label">用户名</label>
                                <label class="input"> <i class="icon-append fa fa-user"></i>
                                    <input type="username" name="username">
                                    <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> 请输入你的用户名</b></label>
                            </section>
                            <section>
                                <label class="label">密码</label>
                                <label class="input"> <i class="icon-append fa fa-lock"></i>
                                    <input type="password" name="password">
                                    <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> 请输入密码</b> </label>
                            </section>

                            <section>
                                <label class="checkbox">
                                    <input type="checkbox" name="auto_login" checked="">
                                    <i></i>记住我</label>
                            </section>
                        </fieldset>
                        <footer>
                            <button type="submit" class="btn btn-primary">
                                登陆
                            </button>
                        </footer>
                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

<!--================================================== -->
<script src="/static/smartadmin/js/libs/jquery-2.0.2.min.js"></script>
<script src="/static/jquery-ui/js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
<script src="/static/js/jquery-ui-zh.js" type="text/javascript"></script>
<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
<script src="/static/smartadmin/js/plugin/pace/pace.min.js"></script>


<!-- JS TOUCH : include this plugin for mobile drag / drop touch events
<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

<!-- BOOTSTRAP JS -->
<script src="/static/smartadmin/js/bootstrap/bootstrap.min.js"></script>

<!-- CUSTOM NOTIFICATION -->
<script src="/static/smartadmin/js/notification/SmartNotification.min.js"></script>

<!-- JARVIS WIDGETS -->
<script src="/static/smartadmin/js/smartwidgets/jarvis.widget.min.js"></script>

<!-- EASY PIE CHARTS -->
<script src="/static/smartadmin/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

<!-- SPARKLINES -->
<script src="/static/smartadmin/js/plugin/sparkline/jquery.sparkline.min.js"></script>

<!-- JQUERY VALIDATE -->
<script src="/static/smartadmin/js/plugin/jquery-validate/jquery.validate.min.js"></script>

<!-- JQUERY MASKED INPUT -->
<script src="/static/smartadmin/js/plugin/masked-input/jquery.maskedinput.min.js"></script>

<!-- JQUERY SELECT2 INPUT -->
<script src="/static/smartadmin/js/plugin/select2/select2.min.js"></script>

<!-- JQUERY UI + Bootstrap Slider -->
<script src="/static/smartadmin/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

<!-- browser msie issue fix -->
<script src="/static/smartadmin/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

<!-- FastClick: For mobile devices -->
<script src="/static/smartadmin/js/plugin/fastclick/fastclick.js"></script>

<!--[if IE 7]>

<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

<![endif]-->

<!-- MAIN APP JS FILE -->
<script src="/static/smartadmin/js/app.js"></script>

<script type="text/javascript">
    runAllForms();

    $(function() {
        // Validation
        $("#login-form").validate({
            // Rules for form validation
            rules : {
                username : {
                    required : true
                },
                password : {
                    required : true,
                    minlength : 3,
                    maxlength : 20
                }
            },

            // Messages for form validation
            messages : {
                username : {
                    required : '请输入你的用户名'
                },
                password : {
                    required : '请输入你的密码'
                }
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
    });
</script>

</body>
</html>
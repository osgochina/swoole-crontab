<?php include __DIR__ . '/../include/header.php'; ?>
<!-- MAIN PANEL -->
<div id="main" role="main">
    <!-- RIBBON -->
    <div id="ribbon"></div>
    <div id="content">
        <!-- START ROW -->
        <div class="row">

            <!-- NEW COL START -->
            <article class="col-sm-12 col-md-12 col-lg-6">

                <!-- Widget ID (each widget will need unique ID)-->
                <div class="jarviswidget"
                     id="wid-id-1"
                     data-widget-colorbutton="false"
                     data-widget-editbutton="false"
                     data-widget-custombutton="false"
                >
                    <header>
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2><strong>修改密码</strong> </h2>

                    </header>

                    <!-- widget div-->
                    <div>

                        <!-- widget edit box -->
                        <div class="jarviswidget-editbox">
                            <!-- This area used as dropdown edit box -->
                        </div>
                        <!-- end widget edit box -->

                        <!-- widget content -->
                        <div class="widget-body no-padding">

                            <form class="smart-form" id="checkout-form" novalidate="novalidate" method="post" action="/password/modifyPassword">
                                <?php include __DIR__ . '/../include/msg.php'; ?>
                                <fieldset>
                                    <section>
                                        <label class="label">当前密码</label>
                                        <label class="input">
                                            <input type="password"   name="oldpassword" id="oldpassword" maxlength="32" class="input-sm" placeholder="请输入当前密码">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">密码</label>
                                        <label class="input">
                                            <input type="password"   name="password" id="password" maxlength="32" class="input-sm" placeholder="请输入密码">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">重复密码</label>
                                        <label class="input">
                                            <input type="password"   name="confirm_password" id="confirm_password" maxlength="32" class="input-sm" placeholder="请再次输入密码">
                                        </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                </footer>
                            </form>
                        </div>
                        <!-- end widget content -->

                    </div>
                    <!-- end widget div -->

                </div>
                <!-- end widget -->

            </article>
            <!-- END COL -->
        </div>
    </div>
    <!-- end content -->
</div>
<!-- end main -->
<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        pageSetUp();
        var $checkoutForm = $('#checkout-form').validate({
            rules : {
                oldpassword : {
                    required : true,
                    minlength: 5
                },
                password : {
                    required : true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                }
            },

            // Messages for form validation
            messages : {
                oldpassword : {
                    required : '请填写当前密码',
                    minlength: "密码长度不能小于 5 个字母"
                },
                password : {
                    required : '请填写密码',
                    minlength: "密码长度不能小于 5 个字母"
                },
                confirm_password: {
                    required: "请输入密码",
                    minlength: "密码长度不能小于 5 个字母",
                    equalTo: "两次密码输入不一致"
                }
            },
            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
        //$("#user_uids").select2();
    });
</script>
</body>
</html>
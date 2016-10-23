<?php include __DIR__.'/../include/header.php'; ?>
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
                        <h2><strong>编辑用户</strong> </h2>

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

                            <form class="smart-form" id="checkout-form" novalidate="novalidate" method="post" action="/user/addoredit">
                                <?php include __DIR__.'/../include/msg.php'; ?>
                                <fieldset>
                                    <input type="hidden" name="id" value="<?php echo isset($id)?$id:0;?>">
                                    <section>
                                        <label class="label">用户名</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($username)?$username:"" ?>" <?php if (isset($id)){?> disabled="disabled" <?php }?>  name="username" id="username" maxlength="32" class="input-sm" placeholder="请输入用户名">
                                        </label>
                                    </section>
                                    <?php if (empty($id) ){?>
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
                                    <?php }?>
                                    <section>
                                        <label class="label">昵称</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($nickname)?$nickname:"" ?>"    name="nickname" id="nickname" maxlength="256" class="input-sm" placeholder="请输入昵称">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">状态</label>
                                        <label class="select">
                                            <select class="input-sm" name="blocking">
                                                <option value="0" <?php if (isset($blocking) && $blocking==0){?> selected="selected" <?php } ?>>可用</option>
                                                <option value="1" <?php if (isset($blocking) && $blocking==1){?> selected="selected" <?php } ?>>禁用</option>
                                            </select>
                                        </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="location.href='/user/index'">
                                        返回
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
                username : {
                    required : true
                },
                password : {
                    required : true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                },
                nickname : {
                    required : true
                }
            },

            // Messages for form validation
            messages : {
                username : {
                    required : '请填写用户名'
                },
                password : {
                    required : '请填写密码',
                    minlength: "密码长度不能小于 5 个字母"
                },
                confirm_password: {
                    required: "请输入密码",
                    minlength: "密码长度不能小于 5 个字母",
                    equalTo: "两次密码输入不一致"
                },
                nickname : {
                    required : '请填写昵称'
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
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
                    <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

                    data-widget-colorbutton="false"
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true"
                    data-widget-sortable="false"

                    -->
                    <header>
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2><strong>编辑分组</strong> </h2>

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

                            <form class="smart-form" id="checkout-form" novalidate="novalidate" method="post" action="/auth/groupedit">
                                <?php include __DIR__.'/../include/msg.php'; ?>
                                <fieldset>
                                    <input type="hidden" name="gid" value="<?php echo isset($gid)?$gid:0;?>">
                                    <section>
                                        <label class="label">分组名</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($gname)?$gname:"" ?>"  name="gname" id="gname" maxlength="32" class="input-sm" placeholder="分组名">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">状态</label>
                                        <label class="select">
                                            <select class="input-sm" name="status">
                                                <option value="0" <?php if (isset($status) && $status==0){?> selected="selected" <?php } ?>>可用</option>
                                                <option value="1" <?php if (isset($status) && $status==1){?> selected="selected" <?php } ?>>无效</option>
                                            </select>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">用户</label>
                                        <label class="select">
                                            <?=$user_uids?>
                                        </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="location.href='/auth/index'">
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
                gname : {
                    required : true
                }
            },

            // Messages for form validation
            messages : {
                gname : {
                    required : '请填写名称'
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

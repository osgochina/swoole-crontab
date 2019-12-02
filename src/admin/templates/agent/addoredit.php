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
                        <h2><strong>编辑Agent</strong> </h2>

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

                            <form class="smart-form" id="checkout-form" novalidate="novalidate" method="post" action="/agent/addoredit">
                                <?php include __DIR__.'/../include/msg.php'; ?>
                                <fieldset>
                                    <input type="hidden" name="id" value="<?php echo isset($id)?$id:0;?>">
                                    <section>
                                        <label class="label">分组</label>
                                        <label class="select">
                                            <?=$gids?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">别名</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($alias)?$alias:"" ?>"  name="alias" id="alias" maxlength="32" class="input-sm" placeholder="别名">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">IP</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($ip)?$ip:"" ?>"  name="ip" id="ip" maxlength="32" class="input-sm" placeholder="IP">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">状态</label>
                                        <label class="select">
                                            <select class="input-sm" name="status">
                                                <option value="0" <?php if (isset($status) && $status==0){?> selected="selected" <?php } ?>>正常</option>
                                                <option value="1" <?php if (isset($status) && $status==1){?> selected="selected" <?php } ?>>暂停</option>
                                            </select>
                                        </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="location.href='/agent/index'">
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
                alias : {
                    required : true
                },
                ip : {
                    required : true
                },
                port : {
                    required : true
                },
                'gids[]' : {
                    required : true
                }
            },
            // Messages for form validation
            messages : {
                'gids[]' : {
                    required : '请选择分组'
                },
                alias : {
                    required : '请填写别名'
                },
                ip : {
                    required : '请填写ip'
                },
                port : {
                    required : '请填写端口'
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

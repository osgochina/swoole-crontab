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
                        <h2><strong>添加任务</strong> </h2>

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

                            <form class="smart-form" id="checkout-form" novalidate="novalidate" method="post" action="/crontab/addoredit">
                                <?php include __DIR__.'/../include/msg.php'; ?>
                                <fieldset>
                                    <input type="hidden" name="id" value="<?php echo isset($id)?$id:0;?>">
                                    <section>
                                        <label class="label">任务名</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($taskname)?$taskname:"" ?>"  name="taskname" id="taskname" maxlength="32" class="input-sm" placeholder="任务名">
                                        </label>
                                    </section>
                                    <section>
<!--                                    <label class="control-label" for="taskgroup">分组:</label>-->
<!--                                    <div class="form-group" style="width: 200px;">-->
<!--                                        <select class="select2" id="taskgroup" name="gid">-->
<!--                                            --><?php //foreach ($group as $k=>$gname): ?>
<!--                                                <option value="--><?//= $k ?><!--"-->
<!--                                                    --><?php //if ( isset($gid) &&$gid == $k) echo 'selected="selected"'; ?><!-- >--><?//= $gname ?><!--</option>-->
<!--                                            --><?php //endforeach; ?>
<!--                                        </select>-->
<!--                                    </div>-->
                                        <section>
                                            <label class="label">分组</label>
                                            <label class="input">
                                                <input type="text" value="<?php echo isset($gname)?$gname:"" ?>"   disabled="disabled" class="input-sm" >
                                            </label>
                                        </section>
                                    </section>
                                    <section>
                                        <label class="label">规则</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($rule)?$rule:"" ?>"   name="rule" id="rule" maxlength="128" class="input-sm" placeholder="规则">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">命令</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($execute)?$execute:"" ?>"    name="execute" id="execute" maxlength="256" class="input-sm" placeholder="命令">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">状态</label>
                                        <label class="select">
                                            <select class="input-sm" name="status">
                                                <option value="0" <?php if (isset($status) && $status==0){?> selected="selected" <?php } ?>>可用</option>
                                                <option value="1" <?php if (isset($status) && $status==1){?> selected="selected" <?php } ?>>暂停</option>
                                            </select>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">并发任务数</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($runnumber)?$runnumber:"0" ?>"  name="runnumber" id="runnumber" maxlength="32" class="input-sm" placeholder="并发任务数">
                                        </label>
                                        <div class="note">
                                            <strong>Note:</strong>并发任务数 0不限制  其他表示限制的数量
                                        </div>
                                    </section>
                                    <section>
                                        <label class="label">进程运行时用户</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($runuser)?$runuser:"" ?>"    name="runuser" id="runuser" maxlength="32" class="input-sm" placeholder="进程运行时用户">
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">负责人</label>
                                        <label class="select">
                                            <?=$manager?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">Agent</label>
                                        <div class="row">
                                            <?php foreach ($agents as $k=>$d){ ?>
                                            <div class="col col-4">
                                                <label class="checkbox state-success"><input type="checkbox" name="agents[]" <?php if (isset($d["checked"]) && $d["checked"]){?> checked="checked"<?php } ?> value="<?=$d["id"]?>"><i></i><?=$d["alias"]?>(<?=$d["ip"]?>)</label>
                                            </div>
                                            <?php }?>
                                        </div>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="location.href='/crontab/index'">
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
        jQuery.validator.addMethod("runuser", function(value, element) {
            console.log(value);
            return "root" != value ;
        }, "运行时用户不能为root");
        var $checkoutForm = $('#checkout-form').validate({
            rules : {
                taskname : {
                    required : true
                },
                rule : {
                    required : true
                },
                'manager[]' : {
                    required : true
                },
                execute : {
                    required : true
                },
                runuser : {
                    required : true,
                    runuser : true
                },
                runnumber:{
                    required : true,
                    digits:true,
                    max:64,
                    min:0
                }

            },

            // Messages for form validation
            messages : {
                taskname : {
                    required : '请填写任务名'
                },
                rule : {
                    required : '请填写规则'
                },
                'manager[]' : {
                    required : '请选择责任人'
                },
                execute : {
                    required : '请填写运行命令'
                },
                runuser : {
                    required : '请填写运行时用户'
                },
                runnumber:{
                    required : '必填项',
                    digits:"必须是数字",
                    max:"最多开64个进程",
                    min:'不能为负数'
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

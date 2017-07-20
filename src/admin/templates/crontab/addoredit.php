<?php include __DIR__.'/../include/header.php'; ?>
<!-- MAIN PANEL -->
<div id="main" role="main">
    <!-- RIBBON -->
    <div id="ribbon"></div>
    <div id="content">
        <!-- START ROW -->
        <div class="row">

            <!-- NEW COL START -->
            <article class="col-sm-12 col-md-12 col-lg-12">

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
                                        <section>
                                            <label class="label">分组</label>
                                            <label class="input">
                                                <input type="text" value="<?php echo isset($gname)?$gname:"" ?>"   disabled="disabled" class="input-sm" >
                                            </label>
                                        </section>
                                    </section>

                                    <div id="tabs">
                                        <ul>
                                            <li>
                                                <a href="#tabs-a">秒</a>
                                            </li>
                                            <li>
                                                <a href="#tabs-b">分钟</a>
                                            </li>
                                            <li>
                                                <a href="#tabs-c">小时</a>
                                            </li>
                                            <li>
                                                <a href="#tabs-d">日</a>
                                            </li>
                                            <li>
                                                <a href="#tabs-e">月</a>
                                            </li>
                                            <li>
                                                <a href="#tabs-f">周</a>
                                            </li>
                                        </ul>
                                        <div id="tabs-a">
                                            <div class="row">
                                                <div class="col col-6">
                                                    <label class="radio state-success"><input type="radio" name="v_second" value="1" ><i></i>每秒 允许的通配符[, - * /]</label>
                                                    <label class="radio state-success">
                                                        <input type="radio" name="v_second" value="2"><i></i>周期,从第<span id="v_secondX_0">X</span>秒到第<span id="v_secondY_0">Y</span>秒
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_secondStart_0" value="0"  type="text">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_secondEnd_0" value="1"  type="text">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_second" value="3"><i></i>从<span id="v_secondX_1">X</span>秒开始,到<span id="v_secondY_1">Y</span>,每<span id="v_secondZ_1">Z</span>秒执行一次</label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_secondStart_1" value="0" type="text">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_secondEnd_1" value="59" type="text">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_secondLoop_1" value="1"   type="text">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_second" value="4"><i></i>勾选具体值</label>
                                                    <?php $n=1; for($i=0;$i<6;$i++){?>
                                                        <div class="row" style="padding-left: 40px">
                                                            <div class="col-md-12">
                                                                <?php for($j=0;$j<10,$n<60;$j++){?>
                                                                    <label class="checkbox-inline v_secondList">
                                                                        <input name="v_secondCheckbox" type="checkbox" for="v_second" class="checkbox style-0" value="<?=$n?>">
                                                                        <span style="margin-left:0px"> <?=$n?> </span>
                                                                    </label>
                                                                    <?php $n++; }?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div id="tabs-b">
                                            <div class="row">
                                                <div class="col col-6">
                                                    <label class="radio state-success"><input type="radio" name="v_min" value="1"><i></i>每分钟 允许的通配符[, - * /]</label>
                                                    <label class="radio state-success">
                                                        <input type="radio" name="v_min" value="2"><i></i>周期,从第<span id="v_minX_0">X</span>分钟到第<span id="v_minY_0">Y</span>分钟
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_minStart_0" type="text" value="0">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_minEnd_0"  type="text" value="1">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_min" value="3"><i></i>
                                                        从<span id="v_minX_1">X</span>分钟开始,到<span id="v_minY_1">Y</span>分钟,每<span id="v_minZ_1">Z</span>执行一次
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_minStart_1"  type="text" value="0">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_minEnd_1" type="text" value="59">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_minLoop_1" value="1"   type="text">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_min" value="4"><i></i>勾选具体值</label>
                                                    <?php $n=0; for($i=0;$i<6;$i++){?>
                                                        <div class="row" style="padding-left: 40px">
                                                            <div class="col-md-12">
                                                                <?php for($j=0;$j<10;$j++){?>
                                                                    <label class="checkbox-inline v_minList">
                                                                        <input name="v_minCheckbox" type="checkbox" for="v_min" class="checkbox style-0" value="<?=$n?>">
                                                                        <span style="margin-left:0px"> <?=$n?> </span>
                                                                    </label>
                                                                    <?php $n++; }?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tabs-c">
                                            <div class="row">
                                                <div class="col col-6">
                                                    <label class="radio state-success"><input type="radio" name="v_hour" value="1" ><i></i>每小时 允许的通配符[, - * /]</label>
                                                    <label class="radio state-success">
                                                        <input type="radio" name="v_hour" value="2"><i></i>周期,从第<span id="v_hourX_0">X</span>小时到第<span id="v_hourY_0">Y</span>小时
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_hourStart_0"  type="text" value="1">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_hourEnd_0"  type="text" value="2">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_hour" value="3"><i></i>
                                                        从<span id="v_hourX_1">X</span>小时开始,到<span id="v_hourY_1">Y</span>小时,每<span id="v_hourZ_1">Z</span>小时执行一次
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_hourStart_1"  type="text" value="1">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_hourEnd_1"  type="text" value="23">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_hourLoop_1" value="1"   type="text">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_hour" value="4"><i></i>勾选具体值</label>
                                                    <?php $n=0; for($i=0;$i<2;$i++){?>
                                                        <div class="row" style="padding-left: 40px">
                                                            <div class="col-md-12">
                                                                <?php for($j=0;$j<12;$j++){?>
                                                                    <label class="checkbox-inline v_hourList">
                                                                        <input name="v_hourCheckbox" type="checkbox" for="v_hour"  class="checkbox style-0" value="<?=$n?>">
                                                                        <span style="margin-left:0px"> <?=$n?> </span>
                                                                    </label>
                                                                    <?php $n++; }?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div id="tabs-d">
                                            <div class="row">
                                                <div class="col col-6">
                                                    <label class="radio state-success"><input type="radio" name="v_day" value="1" ><i></i>日 允许的通配符[, - * /]</label>
                                                    <label class="radio state-success">
                                                        <input type="radio" name="v_day" value="2"><i></i>周期,从第<span id="v_dayX_0">X</span>天到第<span id="v_dayY_0">Y</span>天
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_dayStart_0"  type="text" value="1">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_dayEnd_0"  type="text" value="2">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_day" value="3"><i></i>
                                                        从<span id="v_dayX_1">X</span>天开始,到<span id="v_dayY_1">Y</span>天,每<span id="v_dayZ_1">Z</span>天执行一次
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_dayStart_1"  type="text" value="1">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner"  id="v_dayEnd_1"  type="text" value="31">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_dayLoop_1" value="1"   type="text">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_day" value="4"><i></i>勾选具体值</label>
                                                    <?php $n=1; for($i=0;$i<3;$i++){?>
                                                        <div class="row" style="padding-left: 40px">
                                                            <div class="col-md-12">
                                                                <?php for($j=0;$j<11;$j++){ if ($n >31){break;}?>
                                                                    <label class="checkbox-inline v_dayList">
                                                                        <input name="v_dayCheckbox" type="checkbox" for="v_day" class="checkbox style-0" value="<?=$n?>">
                                                                        <span style="margin-left:0px"> <?=$n?> </span>
                                                                    </label>
                                                                    <?php $n++; }?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div id="tabs-e">
                                            <div class="row">
                                                <div class="col col-6">
                                                    <label class="radio state-success"><input type="radio" name="v_mon" value="1" ><i></i>月 允许的通配符[, - * /]</label>
                                                    <label class="radio state-success">
                                                        <input type="radio" name="v_mon" value="2"><i></i>周期,从第<span id="v_monX_0">X</span>月到第<span id="v_monY_0">Y</span>月
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_monStart_0"  type="text" value="1">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_monEnd_0"  type="text" value="2">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_mon" value="3"><i></i>
                                                        从<span id="v_monX_1">X</span>月开始,到<span id="v_monY_1">Y</span>月,每<span id="v_monZ_1">Z</span>月执行一次
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_monStart_1"  type="text" value="1">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_monEnd_1"  type="text" value="12">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_monLoop_1" value="1"   type="text">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_mon" value="4"><i></i>勾选具体值</label>
                                                    <?php $n=1; for($i=0;$i<1;$i++){?>
                                                        <div class="row" style="padding-left: 40px">
                                                            <div class="col-md-12">
                                                                <?php for($j=0;$j<12;$j++){?>
                                                                    <label class="checkbox-inline v_monList">
                                                                        <input name="v_monCheckbox" type="checkbox" for="v_mon" class="checkbox style-0" value="<?=$n?>">
                                                                        <span style="margin-left:0px"> <?=$n?> </span>
                                                                    </label>
                                                                    <?php $n++; }?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="tabs-f">
                                            <div class="row">
                                                <div class="col col-6">
                                                    <label class="radio state-success"><input type="radio" name="v_week" value="1" ><i></i>周 允许的通配符[, - * /]</label>
                                                    <label class="radio state-success">
                                                        <input type="radio" name="v_week" value="2"><i></i>周期,从星期<span id="v_weekX_0">X</span>到星期<span id="v_weekY_0">Y</span>
                                                    </label>
                                                    <div class="row" style="padding-left: 50px">
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_weekStart_0"  type="text" value="1">
                                                        </div>
                                                        <div class="col-md-1">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input class="form-control spinner-left spinner" id="v_weekEnd_0"  type="text" value="2">
                                                        </div>
                                                    </div>
                                                    <label class="radio state-success"><input type="radio" name="v_week" value="4"><i></i>勾选具体值</label>
                                                    <?php $n=1; for($i=0;$i<1;$i++){?>
                                                        <div class="row" style="padding-left: 40px">
                                                            <div class="col-md-12">
                                                                <?php for($j=0;$j<7;$j++){?>
                                                                    <label class="checkbox-inline v_weekList">
                                                                        <input name="v_weekCheckbox" type="checkbox" for="v_week"  class="checkbox style-0" value="<?=$n?>">
                                                                        <span style="margin-left:0px"> <?=$n?> </span>
                                                                    </label>
                                                                    <?php $n++; }?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <section>
                                        <label class="label">表达式</label>
                                        <label class="input">
                                            <input type="text" value="<?php echo isset($rule)?$rule:"* * * * * *" ?>"
                                                   name="rule" id="rule" onblur="Crontab.init($(this));"
                                                   maxlength="256" class="input-sm" placeholder="规则">
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
                                            <input type="text" value="<?php echo isset($runuser)?$runuser:"nobody" ?>"    name="runuser" id="runuser" maxlength="32" class="input-sm" placeholder="进程运行时用户">
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
<script src="/static/js/crontab.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function() {
        pageSetUp();
        $('#tabs').tabs();
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
        Crontab.bind();
        Crontab.init($("#rule"));
    });
</script>
</body>
</html>

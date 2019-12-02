<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">
    <!-- RIBBON -->
    <div id="ribbon"></div>
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">

                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>定时任务管理</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>

                        <!--每页数量-->
                        <div class="widget-toolbar">
                            <div class="btn-group">
                                <button class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
                                    每页显示 <?=$pager['pagesize']?> 条结果 <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li <?php if (isset($_GET['pagesize']) and $_GET['pagesize'] == '10') echo 'class="active"'; ?>>
                                        <a href="<?=Swoole\Tool::url_merge('pagesize', '10')?>">10</a>
                                    </li>
                                    <li <?php if (isset($_GET['pagesize']) and $_GET['pagesize'] == '20') echo 'class="active"'; ?>>
                                        <a href="<?=Swoole\Tool::url_merge('pagesize', '20')?>">20</a>
                                    </li>
                                    <li <?php if (empty($_GET['pagesize']) or $_GET['pagesize'] == '50') echo 'class="active"'; ?>>
                                        <a href="<?=Swoole\Tool::url_merge('pagesize', '50')?>">50</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </header>
                    <div role="content">
                        <div class="jarviswidget-editbox"></div>
                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar" style="height: 60px;"></div>
                            <div id="dt_basic_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                <div class="dt-top-row">
                                    <div class="dataTables_filter" style="top:-56px">
                                        <form id="checkout-form" class="smart-form" novalidate="novalidate">
                                            <label class="control-label" for="taskgroup">服务列表:</label>
                                            <div class="form-group" style="width: 300px;">
                                                <select class="select2" id="agentid">
                                                    <option value="">全部</option>
                                                    <?php foreach ($agents as $agent): ?>
                                                        <option value="<?= $agent["id"] ?>"
                                                            <?php if ( isset($_GET["agentid"]) && $agent["id"] == $_GET["agentid"]) echo 'selected="selected"'; ?> ><?php echo  $agent["alias"]."(".$agent["ip"].")"; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='form-group'>
                                                <a id='submit' class='btn btn-success' style='padding:6px 12px' href='javascript:void(0)'>查询</a>
                                            </div>
                                            <div class='form-group'>
                                                <a id='submit' class='btn btn-primary' style='padding:6px 12px' href='/crontab/addOrEdit'>添加定时任务</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <table id="data_table_stats" class="table table-hover table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th width="8%">分组名</th>
                                        <th width="8%">任务名</th>
                                        <th width="15%">规则</th>
                                        <th width="5%">并发数</th>
                                        <th width="5%">状态</th>
                                        <th width="20%">命令</th>
                                        <th width="5%">运行时用户</th>
                                        <th width="5%">运行状态</th>
                                        <th width="8%">运行开始时间</th>
                                        <th width="8%">运行结束时间</th>
                                        <th width="10%">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="data_table_body">
                                    <?php
                                    foreach ($list as $d)
                                    {
                                        ?>
                                        <tr height="32">
                                            <td style="display: none;"><?= $d["id"] ?></td>
                                            <td><?= $d["gname"] ?></td>
                                            <td><a href="/termlog/index?taskid=<?=$d['id']?>"><?= $d["taskname"] ?></a></td>
                                            <td>
                                                <?php foreach (explode(" ",$d["rule"])as $v){?>
                                                    <em style="padding:5px;"><?=$v?></em>
                                                <?php  } ?>
                                            </td>
                                            <!--                                            <td>--><?//= $d["rule"] ?><!--</td>-->
                                            <td><?= $d["runnumber"] ?></td>
                                            <td><?= $d["status_f"] ?></td>
                                            <td><?= $d["execute"] ?></td>
                                            <td><?= $d["runuser"] ?></td>
                                            <td><?= $d["runStatus_f"] ?></td>
                                            <td><?= $d["runTimeStart"] ?></td>
                                            <td><?= $d["runUpdateTime"] ?></td>
                                            <td>
                                                <a class="btn btn-primary btn-xs" href="/crontab/addOrEdit?id=<?=$d["id"]?>">编辑</a>
                                                <a class="btn btn-primary btn-xs zanting" href="javascript:void(0)"  tid="<?=$d["id"]?>" status="<?php if ($d["status"] == 0){?>1<?php }else{ ?>0<?php } ?>">
                                                    <?php if ($d["status"] == 0){?>暂停<?php }else{ ?> 开始 <?php } ?>
                                                </a>
                                                <a class="btn btn-primary btn-xs shanchu"  href="javascript:void(0)"  tid="<?=$d["id"]?>">删除</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="pager-box">
                            <?php echo $pager['render'];?>
                        </div>
                    </div>
                    <!-- end widget content -->

                </div>
                <!-- end widget div -->

        </div>
        </article>
        <!-- WIDGET END -->
    </div>
</div>
<!-- end content -->
</div>
<!-- end main -->
<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
</body>
<script  type="text/javascript" >
    $(document).ready(function() {
        pageSetUp();
        $(".zanting").click(function () {
            var tid = $(this).attr("tid");
            var status = $(this).attr("status");
            JUI.confirm("你确定要操作吗？", function (r) {
                if (r){
                    $.ajax({
                        url: '/crontab/addOrEdit',
                        type: 'post',
                        data: {"id":tid,"status":status},
                        dataType: 'json',
                        success: function (suc) {
                            if (suc.code == 0) {
                                //JUI.alter(suc.message);
                                location.reload();
                            } else {
                                JUI.alter("[" + suc.code + "]" + suc.message);
                            }
                        },
                        error: function (err) {
                            JUI.alter("出错了");
                        }
                    });
                }
            });
        });
        $(".shanchu").click(function () {
            var tid = $(this).attr("tid");
            JUI.confirm("你确定要删除吗？", function (r) {
                if (r){
                    $.ajax({
                        url: '/crontab/delete',
                        type: 'post',
                        data: {"id":tid},
                        dataType: 'json',
                        success: function (suc) {
                            if (suc.code == 0) {
                                //JUI.alter(suc.message);
                                location.reload();
                            } else {
                                JUI.alter("[" + suc.code + "]" + suc.message);
                            }
                        },
                        error: function (err) {
                            JUI.alter("出错了");
                        }
                    });
                }
            });
        });
        var OPG = {};
        OPG.filter = <?php echo json_encode($_GET);?>;
        $("#submit").click(function(){
            var val = $("#agentid").val();
            if (val){
                OPG.filter.agentid = val;
            }
            OPG.go();
        });
        OPG.go = function() {
            var url = '/crontab/index/?';
            for (var o in OPG.filter) {
                url += o + '=' + OPG.filter[o] + '&';
            }
            location.href = url;
        };

    });

</script>
</html>

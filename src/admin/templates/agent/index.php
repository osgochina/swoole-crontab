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
                        <h2>任务处理器</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>
                    <div role="content">
                        <div class="jarviswidget-editbox"></div>
                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar" style="height: 60px;"></div>
                            <div id="dt_basic_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                <div class="dt-top-row">
                                    <div class="dataTables_filter" style="top:-56px">
                                        <form id="checkout-form" class="smart-form" novalidate="novalidate">
                                            <label class="control-label" for="taskgroup">分组:</label>
                                            <div class="form-group" style="width: 200px;">
                                                <select class="select2" id="taskgroup">
                                                    <option value="">全部</option>
                                                    <?php foreach ($group as $gid=>$gname): ?>
                                                        <option value="<?= $gid ?>"
                                                            <?php if ( isset($_GET["gid"]) && $gid == $_GET["gid"]) echo 'selected="selected"'; ?> ><?= $gname ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='form-group'>
                                                <a id='submit' class='btn btn-success' style='padding:6px 12px' href='javascript:void(0)'>查询</a>
                                            </div>
                                            <div class='form-group'>
                                                <a id='submit' class='btn btn-primary' style='padding:6px 12px' href='/agent/addOrEdit'>添加CrontabAgent</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <table id="data_table_stats" class="table table-hover table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="10%">名称</th>
                                        <th width="10%">分组</th>
                                        <th width="10%">IP</th>
                                        <th width="10%">PORT</th>
                                        <th width="10%">状态</th>
                                        <th width="10%">是否注册</th>
                                        <th width="10%">心跳时间</th>
                                        <th width="10%">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="data_table_body">
                                    <?php
                                    foreach ($list as $d)
                                    {
                                        ?>
                                        <tr height="32">
                                            <td><?= $d["id"] ?></td>
                                            <td><?= $d["alias"] ?></td>
                                            <td><?php if (is_array($d["gname"])){foreach ($d["gname"] as $gname){
                                                    echo $gname." ";
                                                }}else{echo $d["gname"];} ?></td>
                                            <td><?= $d["ip"] ?></td>
                                            <td><?= $d["port"] ?></td>
                                            <td><?= $d["status_f"] ?></td>
                                            <td><?= $d["isregister_f"] ?></td>
                                            <td><?php if (isset($d["lasttime"])){echo date("Y-m-d H:i:s",$d["lasttime"]);} ?></td>
                                            <td>
                                                <a class="btn btn-primary btn-xs" href="/agent/addOrEdit?id=<?=$d["id"]?>">编辑</a>
                                                <a class="btn btn-primary btn-xs shanchu"  href="javascript:void(0)"  cid="<?=$d["id"]?>">删除</a>
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

        $(".shanchu").click(function () {
            var cid = $(this).attr("cid");
            JUI.confirm("你确定要删除吗？", function (r) {
                if (r){
                    $.ajax({
                        url: '/agent/delete',
                        type: 'post',
                        data: {"cid":cid},
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
            var val = $("#taskgroup").val();
            OPG.filter.gid = val;
            OPG.go();
        });
        OPG.go = function() {
            var url = '/agent/index/?';
            for (var o in OPG.filter) {
                url += o + '=' + OPG.filter[o] + '&';
            }
            location.href = url;
        };
    });

</script>
</html>

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
                        <h2>分组管理</h2>
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
                                            <div class='form-group'>
                                                <a id='submit' class='btn btn-primary' style='padding:6px 12px' href='/crongroup/addoredit'>添加分组</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <table id="data_table_stats" class="table table-hover table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th width="6%">分组ID</th>
                                        <th width="8%">分组名</th>
                                        <th width="10%">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="data_table_body">
                                    <?php
                                    foreach ($list as $gid=>$d)
                                    {
                                        ?>
                                        <tr height="32">
                                            <td><?= $gid ?></td>
                                            <td><?= $d ?></td>
                                            <td>
                                                <a class="btn btn-primary btn-xs" href="/crongroup/addoredit?gid=<?=$gid?>">编辑</a>
                                                <a class="btn btn-primary btn-xs shanchu"  href="javascript:void(0)"  gid="<?=$gid?>">删除</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
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
            var gid = $(this).attr("gid");
            JUI.confirm("你确定要删除吗？", function (r) {
                if (r){
                    $.ajax({
                        url: '/crongroup/delete',
                        type: 'post',
                        data: {"gid":gid},
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

    });

</script>
</html>

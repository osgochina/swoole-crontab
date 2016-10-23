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
                        <h2>运行中任务</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>
                    <div role="content">
                        <div class="jarviswidget-editbox"></div>
                        <div class="widget-body no-padding">
                            <div id="dt_basic_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                <table id="data_table_stats" class="table table-hover table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th width="10%">运行任期</th>
                                        <th width="10%">到期运行时间</th>
                                        <th width="10%">任务ID</th>
                                        <th width="10%">任务名</th>
                                        <th width="10%">运行状态</th>
                                        <th width="10%">查看日志</th>
                                    </tr>
                                    </thead>
                                    <tbody id="data_table_body">
                                    <?php
                                    foreach ($list as $d)
                                    {
                                        ?>
                                        <tr height="32">
                                            <td><?= $d["minute"] ?></td>
                                            <td><?= date("Y-m-d H:i:s",$d["sec"]) ?></td>
                                            <td><?= $d["id"] ?></td>
                                            <td><?= $d["taskname"] ?></td>
                                            <td><?= $d["runStatus_f"] ?></td>
                                            <td>
                                                <?php if ($d["runStatus"] > 0){?>
                                                <a href="/termlog/index?taskid=<?=$d['id']?>&runid=<?=$d['runid']?>">查看日志</a>
                                                <?php }?>
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
    });

</script>
</html>

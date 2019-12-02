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
                        <h2>权限管理</h2>
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

                        <!--排序-->
                        <div class="widget-toolbar">
                            <div class="btn-group">
                                <button class="btn dropdown-toggle btn-xs btn-success" data-toggle="dropdown">
                                    排序选项 <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li <?php if (isset($_GET['order']) and $_GET['order'] == 'gid@asc') echo 'class="active"'; ?>>
                                        <a href="<?=Swoole\Tool::url_merge('order', 'gid@asc')?>">升序</a>
                                    </li>
                                    <li <?php if (empty($_GET['order']) or $_GET['order'] == 'gid@desc') echo 'class="active"'; ?>>
                                        <a href="<?=Swoole\Tool::url_merge('order', 'gid@desc')?>">降序</a>
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
                                            <div class='form-group'>
                                                <a id='submit' class='btn btn-primary' style='padding:6px 12px' href='/auth/groupedit'>添加分组</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <table id="data_table_stats" class="table table-hover table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th width="20%">分组id</th>
                                        <th width="20%">分组名</th>
                                        <th width="20%">状态</th>
                                        <th width="20%">最近修改时间</th>
                                        <th width="20%">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="data_table_body">
                                    <?php
                                    foreach ($list as $d)
                                    {
                                        ?>
                                        <tr height="32">
                                            <td><?= $d["gid"] ?></td>
                                            <td><?= $d["gname"] ?></td>
                                            <td><?php echo  $d["status"]==0?"正常":"无效"; ?></td>
                                            <td><?= $d["lastupdate"] ?></td>
                                            <td>
                                                <a class="btn btn-primary btn-xs" href="/auth/groupedit?gid=<?=$d["gid"]?>">编辑</a>
                                                <a class="btn btn-primary btn-xs" href="/auth/nodeedit?gid=<?=$d["gid"]?>">修改权限</a>
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

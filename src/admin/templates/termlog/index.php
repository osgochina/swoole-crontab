<?php include __DIR__.'/../include/header.php'; ?>
<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon"></div>
    <!-- END RIBBON -->

    <!-- MAIN CONTENT -->
    <div id="content">

        <!-- Bread crumb is created dynamically -->
        <!-- row -->
        <div class="row">
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget" id="wid-id-3" data-widget-colorbutton="false" data-widget-editbutton="false">
                <header>
                    <span class="widget-icon"> <i class="fa fa-eye-slash"></i> </span>
                    <h2>查询条件</h2>
                </header>
                <!-- widget div-->
                <div>
                    <!-- widget content -->
                    <div class="widget-body">
                        <form>
                            <fieldset>
                                <div class="form-group col-sm-3">
                                    <label>TaskId</label>
                                    <input class="form-control"  id="taskid" placeholder="TaskId" type="text" value="<?=$this->value($_GET, 'taskid')?>">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>RunId</label>
                                    <input class="form-control" type="text" name="runid" id="runid" placeholder="RunId" value="<?=$this->value($_GET, 'runid')?>">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>开始时间</label>
                                    <input type="text" class="form-control datepicker" data-dateformat="yy-mm-dd" id="begin_date" readonly="readonly" value="<?=$this->value($_GET, 'begin_date')?>"/>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>结束时间</label>
                                    <input type="text" class="form-control datepicker" data-dateformat="yy-mm-dd" id="end_date" readonly="readonly" value="<?=$this->value($_GET, 'end_date')?>"/>
                                </div>
                                <div class='form-group col-sm-3'>
                                    <a id='submit' class='btn btn-success' style='padding:6px 12px' href='javascript:void(0)'>查询</a>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <!-- end widget content -->

                </div>
                <!-- end widget div -->
            </div>
            <!-- end widget -->
            <div class="col-sm-12">

                <div class="well">
                    <table class="table table-striped table-forum">
                        <tbody>
                        <!-- Post -->
                        <?php foreach ($list as $d){?>
                        <tr>
                            <td class="text-center">
                                   <b style="color: #6fb679"><?=$d["createtime"]?></b>
                            </td>
                            <td><strong style="color: #3276b1;"><?=$d["explain"]?></strong>&nbsp;[<small title="runid"><?=$d["runid"]?></small>]</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="width: 12%;" >
                                <strong><?=$d["taskname"]?></strong>
                                <small title="taskid"><?=$d["taskid"]?></small>
                            </td>
                            <td>
                                <?=$d["msg"]?>
                            </td>
                        </tr>
                        <?php } ?>
                        <!-- end Post -->
                        </tbody>
                    </table>
                    <div class="pager-box">
                        <?php echo $pager['render'];?>
                    </div>
                </div>
            </div>

        </div>

        <!-- end row -->

    </div>
    <!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->
<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
</body>
<script  type="text/javascript" >
    $(document).ready(function() {
        pageSetUp();
        var OPG = {};
        OPG.filter = <?php echo json_encode($_GET);?>;
        $("#submit").click(function(){
            var val = $("#taskid").val();
            OPG.filter.taskid = val;
            var runid = $("#runid").val();
            OPG.filter.runid = runid;
            var begin_date = $("#begin_date").val();
            OPG.filter.begin_date = begin_date;
            var end_date = $("#end_date").val();
            OPG.filter.end_date = end_date;
            OPG.go();
        });
        OPG.go = function() {
            var url = '/termlog/index/?';
            for (var o in OPG.filter) {
                url += o + '=' + OPG.filter[o] + '&';
            }
            location.href = url;
        };
    });

</script>
</html>
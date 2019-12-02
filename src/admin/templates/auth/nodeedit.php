<?php include __DIR__.'/../include/header.php'; ?>
<!-- MAIN PANEL -->
<div id="main" role="main">
    <!-- RIBBON -->
    <div id="ribbon"></div>
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- NEW WIDGET START -->
            <article class="col-sm-12">

                <!-- Widget ID (each widget will need unique ID)-->
                <div class="jarviswidget" id="wid-id-8" data-widget-colorbutton="false" data-widget-editbutton="false">
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
                        <span class="widget-icon"> <i class="fa fa-columns"></i> </span>
                        <h2>修改权限</h2>
                    </header>
                    <!-- widget div-->
                    <div>

                        <!-- widget edit box -->
                        <div class="jarviswidget-editbox">
                            <!-- This area used as dropdown edit box -->

                        </div>
                        <!-- end widget edit box -->

                        <!-- widget content -->
                        <div class="widget-body">
                            <form class="smart-form" id="checkout-form" novalidate="novalidate" method="post" action="/auth/nodeedit">
                                <?php include __DIR__.'/../include/msg.php'; ?>
                                <input type="hidden" name="gid" value="<?php echo isset($gid)?$gid:0;?>">
                                <fieldset>
                                    <?php foreach ($list as $key=>$value){ ?>
                                    <section>
                                        <label class="label"><strong><?=$value["describe"]?></strong></label>
                                        <div class="inline-group">
                                            <label class="checkbox">
                                                <input type="checkbox" class="checkall" name="<?php echo $key;?>" <?php if ( isset($value["checked"])){ ?>  checked="checked" <?php }?>>
                                                <i></i>全部</label>
                                            <?php foreach($value["methods"] as $k=>$v){?>
                                            <label class="checkbox">
                                                <input type="checkbox" class="<?php echo str_replace("\\","",$key);?>" name="<?php echo $key."::".$k;?>" <?php if ( isset($value["checked"]) || isset($v["checked"])){ ?>  checked="checked" <?php }?>>
                                                <i></i><?=$v["describe"]?></label>
                                            <?php } ?>
                                        </div>
                                    </section>
                                    <?php }?>
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
            <!-- WIDGET END -->

        </div>
        <!-- end row -->
    </div>
    <!-- end content -->
</div>
<!-- end main -->
<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".checkall").click(function(){
            var name = $(this).attr("name");
            name = str_replace("\\","",name);
            if($(this).prop("checked") == true){
                $("."+name).prop("checked",true);
            }else{
                $("."+name).prop("checked",false);
            }
        });
    });
</script>
</body>
</html>

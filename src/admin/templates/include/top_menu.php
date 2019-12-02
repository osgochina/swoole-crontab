<div id="logo-group">
    <span><img style="vertical-align:top; padding: 8px" width="35"
               src="<?= Swoole::$php->config['common']['logo_url'] ?>"/></span>
    <span id="logo" style="margin-left: 0px; width: 120px;"><strong
            style="font-size: 18px;"><?= Swoole::$php->config['common']['site_name'] ?></strong></span>
</div>
<div id="project-context">
    <span class="label">当前项目：</span>
        <span id="project-selector" class="popover-trigger-element dropdown-toggle" data-toggle="dropdown">
            <?= $_gname ?> <i class="fa fa-angle-down"></i></span>
    <ul class="dropdown-menu">
        <?php foreach ($_group as $k=>$p): ?>
            <li <?php if ($k == $_gid)
            {
                echo "class='active'";
            } ?>>
                <a href="/page/switch_group/?gid=<?= $k ?>&gname=<?= $p ?>&refer=<?=$_SERVER['REQUEST_URI']?>"><?= $p ?></a>
            </li>
        <?php endforeach; ?>
        <!--            <li class="divider"></li>-->
        <!--            <li>-->
        <!--                <a href="javascript:void(0);"><i class="fa fa-power-off"></i> Clear</a>-->
        <!--            </li>-->
    </ul>
</div>
<div class="pull-right" style="padding: 15px;">
            <span style="font-weight: bolder">
        <span style="text-transform: none;">
                    <a style="text-decoration: none" href="javascript:void(0);"><?= $this->userinfo['nickname'] ?>
                        (<?= $this->userinfo['username'] ?>)
        </span>
        <span style="text-transform: none;padding: 15px 5px;">
                    <a style="text-decoration: none;font-weight: bolder" href="/page/logout/">退出</a>
        </span>
    </span>
</div>
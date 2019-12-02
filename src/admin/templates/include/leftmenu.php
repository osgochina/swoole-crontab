<nav>
<ul>
    <?php if ($this->isShowMenu("crontab","index")){?>
    <li <?php if ($this->isActiveMenu('crontab')){ ?>class="active"<?php } ?>>
        <a href="/crontab/index/" id="crontab_index"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">定时任务管理</span></a>
    </li>
    <?php }?>

    <?php if ($this->isShowMenu("agent","index")){?>
        <li <?php if ($this->isActiveMenu('agent')){ ?>class="active"<?php } ?>>
            <a href="/agent/index/" id="agent_index"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">CronAgent管理</span></a>
        </li>
    <?php }?>
    <?php if ($this->isShowMenu("runtimetask","index")){?>
        <li <?php if ($this->isActiveMenu('runtimetask')){ ?>class="active"<?php } ?>>
            <a href="/runtimetask/index/" id="runtimetask_index"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">运行时任务</span></a>
        </li>
    <?php }?>
    <?php if ($this->isShowMenu("termlog","index")){?>
        <li <?php if ($this->isActiveMenu('termlog')){ ?>class="active"<?php } ?>>
            <a href="/termlog/index/" id="termlog_index"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">运行日志</span></a>
        </li>
    <?php }?>
    <li>
    <a href="#"><i class="fa fa-lg fa-fw fa-cog"></i> <span class="menu-item-parent">系统管理</span></a>
    <ul>
        <?php if ($this->isShowMenu("auth","index")){?>
        <li  <?php if ($this->isActiveMenu('auth')){ ?>class="active"<?php } ?>>
            <a href="/auth/index/" id="auth_index_link"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">权限管理</span></a>
        </li>
        <?php }?>
        <?php if ($this->isShowMenu("crongroup","index")){?>
            <li <?php if ($this->isActiveMenu('crongroup')){ ?>class="active"<?php } ?>>
                <a href="/crongroup/index/" id="crongroup_index"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">分组管理</span></a>
            </li>
        <?php }?>
        <?php if ($this->isShowMenu("user","index")){?>
            <li  <?php if ($this->isActiveMenu('user')){ ?>class="active"<?php } ?>>
                <a href="/user/index/" id="user_index_link"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">用户管理</span></a>
            </li>
        <?php }?>
    </ul>
    </li>
    </li>
    <li <?php if ($this->isActiveMenu('password')){ ?>class="active"<?php } ?>>
        <a href="/password/modifypassword/" ><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">修改密码</span></a>
    </li>
    <li>
</ul>
</nav>

<div class="form-group" style="width: 300px;">
    <select class="select2" id="module_id">
        <option value="">所有模块</option>
        <?php foreach ($modules as $m): ?>
            <option value="<?= $m['id'] ?>: <?= $m['name'] ?>"
                <?php if ($m['id'] == $_GET['module_id']) echo 'selected="selected"'; ?> ><?= $m['id'] ?>
                : <?= $m['name'] ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group" style="width: 400px;">
    <select id="interface_id" class="select2">
        <option value="">所有接口</option>
        <?php foreach ($interfaces as $m): ?>
            <option value="<?= $m['id'] ?>: <?= (empty($m['alias']) ? $m['name'] : $m['alias']) ?>"
                <?php if ($m['id'] == $_GET['interface_id']) echo 'selected="selected"'; ?> >
                <?= $m['id'] ?>: <?= (empty($m['alias']) ? $m['name'] : $m['alias']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    时间：
    <label class="select">
        <select class="input-sm" id="filter_hour_s" onchange="StatsG.filterByHour(<?=(empty($force_reload)?'false':'true')?>)">
            <option value='00' selected="selected">00</option>
            <?php
            for ($i = 1; $i < 24; $i++)
            {
                $v = $i >= 10 ? $i : '0' . $i;
                $select = (!empty($_GET['hour_start']) and $v == $_GET['hour_start']) ? 'selected="selected"' : '';
                echo "<option value='$v' $select>$v</option>\n";
            }
            ?>
        </select>
    </label> ~
    <label class="select">
        <select class="input-sm" id="filter_hour_e" onchange="StatsG.filterByHour(<?=(empty($force_reload)?'false':'true')?>)">
            <?php
            for ($i = 0; $i < 23; $i++) {
                $v = $i >= 10 ? $i : '0' . $i;
                echo "<option value=$v>$v</option>\n";
            }
            ?>
            <option value='23' selected="selected">23</option>
        </select>
    </label>
</div>
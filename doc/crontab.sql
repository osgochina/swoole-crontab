SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for agents
-- ----------------------------
DROP TABLE IF EXISTS `agents`;
CREATE TABLE `agents` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `alias` varchar(64) NOT NULL COMMENT '别名',
  `ip` varchar(20) NOT NULL,
  `status` tinyint(5) NOT NULL DEFAULT '0' COMMENT '0 正常 1暂停',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of agents
-- ----------------------------
INSERT INTO `agents` VALUES ('1', 'Crontab服务', '127.0.0.1', '0');

-- ----------------------------
-- Table structure for agent_group
-- ----------------------------
DROP TABLE IF EXISTS `agent_group`;
CREATE TABLE `agent_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gid` int(10) NOT NULL,
  `aid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of agent_group
-- ----------------------------
INSERT INTO `agent_group` VALUES ('1', '1', '1');

-- ----------------------------
-- Table structure for crongroup
-- ----------------------------
DROP TABLE IF EXISTS `crongroup`;
CREATE TABLE `crongroup` (
  `gid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gname` varchar(32) NOT NULL,
  `manager` varchar(255) DEFAULT NULL COMMENT '负责人',
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of crongroup
-- ----------------------------
INSERT INTO `crongroup` VALUES ('1', '本机', null);

-- ----------------------------
-- Table structure for crontab
-- ----------------------------
DROP TABLE IF EXISTS `crontab`;
CREATE TABLE `crontab` (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `gid` int(10) NOT NULL,
  `taskname` varchar(64) NOT NULL,
  `rule` varchar(256) NOT NULL COMMENT '规则 可以是crontab规则也可以是启动的间隔时间',
  `runnumber` tinyint(5) NOT NULL DEFAULT '0' COMMENT '并发任务数 0不限制  其他表示限制的数量',
  `timeout` int(11) NOT NULL DEFAULT '0' COMMENT '脚本超时时间(单位是秒)',
  `execute` varchar(512) NOT NULL COMMENT '运行命令行',
  `status` tinyint(5) NOT NULL DEFAULT '0' COMMENT ' 0正常 1 暂停',
  `runuser` varchar(32) NOT NULL COMMENT '进程运行时用户',
  `manager` varchar(255) DEFAULT NULL COMMENT '负责人',
  `agents` varchar(1024) DEFAULT NULL,
  `createtime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of crontab
-- ----------------------------
INSERT INTO `crontab` VALUES ('3097968986801831941', '1', '测试任务', '* * * * *', '0','0','/bin/echo &#039;hello swoole-crontab&#039;', '0', 'nobody', 'admin', '1', '2016-10-23 12:45:27', null);

-- ----------------------------
-- Table structure for group_user
-- ----------------------------
DROP TABLE IF EXISTS `group_user`;
CREATE TABLE `group_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of group_user
-- ----------------------------
INSERT INTO `group_user` VALUES ('25', '1', '1');

-- ----------------------------
-- Table structure for rbac_group
-- ----------------------------
DROP TABLE IF EXISTS `rbac_group`;
CREATE TABLE `rbac_group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(32) NOT NULL COMMENT '分组名',
  `status` tinyint(5) NOT NULL COMMENT '状态 0 正常 1不正常',
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`gid`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_group
-- ----------------------------
INSERT INTO `rbac_group` VALUES ('1', '管理员', '0', '2016-10-23 12:37:55');

-- ----------------------------
-- Table structure for rbac_node
-- ----------------------------
DROP TABLE IF EXISTS `rbac_node`;
CREATE TABLE `rbac_node` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL COMMENT '分组id',
  `node` varchar(255) NOT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_node
-- ----------------------------
INSERT INTO `rbac_node` VALUES ('1', '1', 'App\\Controller\\Crontab');
INSERT INTO `rbac_node` VALUES ('2', '1', 'App\\Controller\\Auth');
INSERT INTO `rbac_node` VALUES ('3', '1', 'App\\Controller\\User');
INSERT INTO `rbac_node` VALUES ('4', '1', 'App\\Controller\\Crongroup');
INSERT INTO `rbac_node` VALUES ('5', '1', 'App\\Controller\\Runtimetask');
INSERT INTO `rbac_node` VALUES ('6', '1', 'App\\Controller\\Agent');
INSERT INTO `rbac_node` VALUES ('7', '1', 'App\\Controller\\Termlog');

-- ----------------------------
-- Table structure for rbac_user
-- ----------------------------
DROP TABLE IF EXISTS `rbac_user`;
CREATE TABLE `rbac_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `lastip` varchar(20) DEFAULT NULL,
  `blocking` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否禁用 0 未禁用1禁用',
  `createtime` datetime NOT NULL,
  `lastupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_user
-- ----------------------------
INSERT INTO `rbac_user` VALUES ('1', 'admin', 'dd94709528bb1c83d08f3088d4043f4742891f4f', 'Admin管理员', '2016-10-23 12:51:22', '192.168.244.2', '0', '2016-09-18 22:13:40', '2016-10-23 20:51:27');

-- ----------------------------
-- Table structure for rbac_user_group
-- ----------------------------
DROP TABLE IF EXISTS `rbac_user_group`;
CREATE TABLE `rbac_user_group` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_user_group
-- ----------------------------
INSERT INTO `rbac_user_group` VALUES ('1', '1', '1');

-- ----------------------------
-- Table structure for term_logs
-- ----------------------------
DROP TABLE IF EXISTS `term_logs`;
CREATE TABLE `term_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `taskid` bigint(20) NOT NULL,
  `runid` bigint(20) NOT NULL,
  `explain` varchar(64) NOT NULL,
  `msg` longtext,
  `createtime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `taskid` (`taskid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of term_logs
-- ----------------------------

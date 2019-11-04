swoole-boot
==========

* [依赖软件包地址](#依赖软件包地址)
* [服务基础架构设计](#服务基础架构设计)
* [安装方式](#安装方式)
* [进程管理](#进程管理)
* [目录介绍](#目录介绍)

依赖软件包地址
=============

[依赖软件包地址](https://github.com/swoole-boot/soft)

* 1.依赖安装方式可以参考上方地址
* 2.本项目应用不依赖nginx，具体依赖项可以参考[cockroach/swoole-boot](https://packagist.org/packages/cockroach/swoole-boot)

[回到目录](#swoole-boot)

服务基础架构设计
===============

![架构图](https://github.com/swoole-boot/swoole-boot/blob/master/swoole-boot-micro-server.png?raw=true)

[回到目录](#swoole-boot)

安装方式
========

```bash
#composer create-project cockroach/elephant 目录 版本
#如：
composer create-project cockroach/swoole-boot boot 1.0.2
```

[回到目录](#swoole-boot)

进程管理
=======

```bash
#启动
/usr/local/php/bin/php /yourpath/app/boot.php start

#停止
/usr/local/php/bin/php /yourpath/app/boot.php stop

#重启
/usr/local/php/bin/php /yourpath/app/boot.php restart
```

[回到目录](#swoole-boot)

目录介绍
========

```
-- yourpath       微服务项目目录

  -- app              应用目录
     -- conf            配置               
     -- funcs           函数目录,支持子目录             
     -- logic           业务逻辑层            
     -- models          模型,数据访问层   
          
  -- framework        源码目录
     -- db                数据库驱动
     -- dispatcher        调度器
     -- route             路由
     -- server            服务
     -- Application.php   应用类
     -- Context.php       协程上下文
     -- Func.php          函数基类
     -- Error.php         错误与异常处理
```

[回到目录](#swoole-boot)

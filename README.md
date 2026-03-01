<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

# 🚀 QuickShare - 快捷文件分享与管理系统

QuickShare 是一款基于最新 Laravel 10 生态构建的现代化、高效的**文件快捷分享平台**。它致力于提供类似"丰巢快递柜"般体验的文件交换服务——用户上传文件后生成一个独立的"取件码"，其他人只需输入该取件码即可极速下载对应文件。

系统内部配备了完善的用户身份验证、异步队列处理机制、安全过滤以及专为管理员打造的全局文件监控与用户管理面板。通过本次更新，项目已被完整容器化，让你告别繁琐的 PHP、Node.js 与数据库配置，实现 **Docker Compose 一键极简部署**！

---

## 🎯 系统核心特性与功能

### 1. 📦 核心文件流转
- **极速上传下载**: 支持拖拽或点击上传，并自动分配安全且经过筛选校验的 6 位数字/字母组合"取件码"。
- **取件码过滤算法**: 拥有复杂的防撞库及安全校验逻辑（排除纯连续字母/数字，排除所有字符完全一致的弱取件码），初始即在数据库池中生成并预置 100,000 个安全取件码。
- **生命周期管理**: 到期未领取的取件码及文件会自动通过 Laravel Scheduler 与 Queue Worker 任务进行安全销毁，不留任何痕迹。

### 2. 🛡️ 账户与权限控制
- **基础身份管理**: 包含完整的用户注册、登录、找回密码及邮件验证流程。
- **仪表盘体验**: 已登录用户拥有独立的专属 Dashboard，可以统一查看、预览及管理自身所上传的所有文件历史，并支持删除或修改状态。

### 3. 👑 管理员监控全景面板
- **全局仪表盘 (`/admin`)**: 图表化展示当前系统文件总数、占用空间及活跃用户。
- **超级管理员调试台 (`/test-debug`)**: 专属的高级调试路由，集成了文件服务测试、随机取件码测试、手动强制清理触发器和全局邮件系统探测等硬核功能。
- **用户审查与封禁**: 管理员可实时监控系统内异常用户行为，并一键封禁恶意用户的账号及附带权限。

### 4. ⚙️ 技术栈与现代化架构
- **后端驱动**: `PHP 8.2` + `Laravel 10`
- **前端展现**: 采用 `Tailwind CSS` 样式系统，并通过 `Alpine.js` 提供轻量级响应交互，构建链路采用 `Vite` 实现毫秒级热更新。
- **持久化层**: `MySQL 8.0` 作为主数据源，利用 `Redis` 作为极速缓存系统及异步消息任务队列的驱动。
- **辅助服务**: 开发环境内置 `Mailpit` 邮件陷阱服务器，拦截所有发出的验证/通知邮件，便于本地无损调试。

---

## 🐳 Docker 一键极简部署指南

由于本项目依赖 PHP、MySQL、Redis 以及常驻后端的队列调度守护进程等复杂的环境配置。为方便使用，我们强烈建议使用提供的 **Docker Compose** 方案。

### 准备环境
请确保你的电脑或服务器中已经安装了以下底层软件：
- **[Docker Engine](https://docs.docker.com/get-docker/)**
- **[Docker Compose](https://docs.docker.com/compose/install/)**

> **注意：如果你的 3306 端口被本地其他的 MySQL 占用，容器映射已经自动将其调整到了 `13306`，不会与你本地的数据冲突。**

### 部署步骤

#### 1. 一键启动所有服务
打开终端，进入本项目根目录，直接执行：

```bash
docker-compose up -d --build
```

> **提示**: 
> - 系统会自动创建 `.env` 配置文件、生成 `APP_KEY`、运行数据库迁移
> - 整个编排文件会同时启动 6 个容器微服务（主程序 Web、MySQL 数据库、Redis、Mailpit邮件陷阱、队列处理器 Queue Worker 以及定时任务处理进程 Scheduler）
> - 首次启动由于需要拉取镜像、安装依赖，可能需要几分钟

#### 2. 系统核心数据初始化（仅限首次运行）
系统成功跑起来后，还需要初始化 10 万个取件码和超级管理员账户：

```bash
docker-compose exec app php artisan db:seed --force
```

> ⚠️ **重要注意**: 这是一项高密度的后台计算。在生成及安全过滤这 100,000 个取件码时，命令可能需要挂起执行 **30秒 ~ 1分钟左右**，请耐心等待命令行执行完毕（不要中断退出）。

#### 3. 访问你的全新文件平台
一切就绪！你可以打开浏览器进行访问：

| 服务 | 访问地址 | 说明 |
|------|---------|------|
| **QuickShare 门户** | [http://localhost:8000](http://localhost:8000) | 项目主页，可进行上传和下载操作 |
| **测试邮件捕获器** | [http://localhost:8025](http://localhost:8025) | Mailpit Web端，可查看系统所有发出的邮件内容 |

> **🔥 默认超管账户登录信息:**
> - **登录账号**: `admin@admin.com`
> - **登录密码**: `admin`
> - **后台路径**: `/admin` 及高级调试路由 `/test-debug`

---

## 💻 常用运维与管理命令

在 Docker 容器化环境下，所有原有的 `php artisan` 命令都可以通过 `docker-compose exec app` 前缀的方式进行桥接调用：

```bash
# 查看各个容器的健康与运行状态
docker-compose ps

# 实时监控 Web 应用主程序的运行日志
docker-compose logs -f app

# 实时监控 Laravel 队列消费处理进程的日志（查看异步邮件或过期清理日志）
docker-compose logs -f queue-worker

# 💣 【危险】彻底重置并清空整个数据库，同时重新运行所有的生成填充
docker-compose exec app php artisan migrate:fresh --seed

# 清除框架路由、视图和配置的系统缓存
docker-compose exec app php artisan optimize:clear
```

### 关闭与下线

```bash
# 停止运行所有项目服务（但不丢失上传的文件和数据库数据）
docker-compose down

# 💣 【危险】停止服务并彻底摧毁所有数据库及 Redis 的持久化卷数据（文件数据亦会丢失）
docker-compose down -v
```

---

## 📁 目录结构说明

- `app/` - 系统的核心业务逻辑（Controllers, Models, Middleware 等）
- `config/` - 配置文件集合
- `database/` - 数据库迁移文件 (Migrations) 和数据填充脚本 (Seeders, 包括那 10 万个取件码的生成逻辑)
- `public/` - 前端构建后打包的静态入口，以及系统自动建立的 `/storage` 软链接目标点
- `resources/` - 所有的 Blade 页面模板 (`resources/views`)、CSS 样式 (`resources/css`) 与 JS 脚本 (`resources/js`)
- `routes/` - HTTP 路由注册控制 (`web.php` 等)
- `storage/` - 系统日志、框架缓存以及 **用户所有真实上传的文件数据落地位置**
- `docker-compose.yml` & `Dockerfile` - 容器化部署核心编排文件
- `docker-entrypoint.sh` - 容器启动时的自动配置引导挂载脚本（自动创建 .env、生成密钥、运行迁移）

---

*Enjoy sharing your files with QuickShare! 🚀*

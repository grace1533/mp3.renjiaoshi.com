# JYMusic ThinkPHP 8.0 应用

基于 ThinkPHP 8.0 + PHP 8.3 重构的音乐分享平台

## 环境要求

- PHP >= 8.3
- MySQL >= 5.7 (推荐 8.0)
- Composer >= 2.0
- Web 服务器 (Nginx/Apache)

## 安全特性

### 已修复的安全漏洞

1. **SQL 注入防护**
   - 全面使用参数化查询
   - 所有数据库操作使用 ORM/Query Builder
   - 输入数据严格验证

2. **XSS 攻击防护**
   - 全局 XSS 过滤中间件
   - 输出自动 HTML 实体编码
   - Content-Security-Policy 响应头

3. **CSRF 防护**
   - Token 验证机制
   - Session 签名验证

4. **文件上传安全**
   - 严格的文件类型验证
   - 文件大小限制
   - 随机文件名生成

5. **认证安全**
   - 密码加盐哈希存储
   - Session 固定攻击防护
   - Token 过期机制

6. **请求安全**
   - 恶意 User-Agent 拦截
   - 请求频率限制
   - URL 路径遍历防护

## 安装步骤

### 1. 克隆项目

```bash
cd /workspace/tp8_new
```

### 2. 安装依赖

```bash
composer install --optimize-autoloader
```

### 3. 配置环境变量

复制 `.env.example` 为 `.env` 并修改配置：

```bash
cp .env.example .env
```

编辑 `.env` 文件：

```ini
APP_DEBUG = true
APP_URL = http://localhost

DB_TYPE = mysql
DB_HOSTNAME = 127.0.0.1
DB_DATABASE = jymusic
DB_USERNAME = root
DB_PASSWORD = your_password
DB_HOSTPORT = 3306
DB_CHARSET = utf8mb4
DB_PREFIX = jy_
```

### 4. 创建数据库

```sql
CREATE DATABASE jymusic DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. 导入数据表结构

```bash
mysql -u root -p jymusic < database.sql
```

或者使用 PHP 执行：

```php
// 执行 database.sql 中的 SQL 语句
```

### 6. 设置目录权限

```bash
chmod -R 775 storage/
chmod -R 775 public/
```

### 7. 配置 Web 服务器

#### Nginx 配置

```nginx
server {
    listen 80;
    server_name localhost;
    root /workspace/tp8_new/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### Apache 配置

确保启用 `mod_rewrite`，`.htaccess` 文件已包含在 `public/` 目录中。

### 8. 访问应用

浏览器访问：`http://localhost`

## 默认管理员账号

- 用户名：`admin`
- 密码：`admin123`

**请首次登录后立即修改密码！**

## 目录结构

```
tp8_new/
├── app/                    # 应用目录
│   ├── controller/        # 控制器
│   ├── model/            # 模型
│   ├── middleware/       # 中间件
│   ├── validate/         # 验证器
│   └── service/          # 服务类
├── config/               # 配置文件
├── public/               # 公共目录
├── route/                # 路由定义
├── storage/              # 运行时目录
│   ├── cache/           # 缓存
│   ├── log/             # 日志
│   ├── runtime/         # 临时文件
│   └── template/        # 模板缓存
├── vendor/               # Composer 依赖
├── composer.json         # Composer 配置
├── index.php             # 入口文件
└── database.sql          # 数据库脚本
```

## 开发指南

### 创建新控制器

```php
namespace app\controller;

class Music extends BaseController
{
    public function index()
    {
        return $this->fetch();
    }
}
```

### 创建新模型

```php
namespace app\model;

class Songs extends BaseModel
{
    protected $table = 'jy_songs';
}
```

### 创建新验证器

```php
namespace app\validate;

class Music extends Validate
{
    protected $rule = [
        'name' => 'require|length:1,100',
    ];
}
```

## 常见问题

### 1. 权限错误

确保 `storage/` 和 `public/` 目录可写：

```bash
chmod -R 775 storage/ public/
```

### 2. 数据库连接失败

检查 `.env` 文件中的数据库配置是否正确。

### 3. 页面空白

开启调试模式查看错误信息：

```ini
APP_DEBUG = true
```

## 升级说明

本项目是从 ThinkPHP 5.0 升级到 ThinkPHP 8.0 的重构版本，主要变更：

1. PHP 版本要求从 5.4+ 提升到 8.3+
2. 全面使用 Facade 和依赖注入
3. 中间件系统重构
4. 路由定义方式变更
5. 模型操作 API 更新

## 许可证

Apache-2.0 License

## 联系方式

如有问题请提交 Issue 或联系开发团队。

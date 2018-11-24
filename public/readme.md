## User Guide

配置:

下载到该工程后，首先执行

    composer update

然后在app.php文件中的providers中查看是否有以下配置，没有则添加：

    'Way\Generators\GeneratorsServiceProvider',
    'Barryvdh\Debugbar\ServiceProvider',
    'Intervention\Image\ImageServiceProvider',
    'Johntaa\Captcha\CaptchaServiceProvider',

在app.php的alias中添加：

    'Carbon'          => 'Carbon\Carbon',
    'Image'           => 'Intervention\Image\Facades\Image',
    'Identicon'       => 'Identicon\Identicon',
    'Captcha'         => 'Johntaa\Captcha\Facades\Captcha',

执行
    php artisan generate:publish-templates

将generate扩展的模板拷贝到app/templates目录下，然后你可以自己修改想要的模板文件

app/commands目录下新增Illuminate\Database\Schema\Grammars\MySqlGrammar.php文件，覆盖默认的函数，让Laravel 4.1的数据库迁移支持MySql字段注释；

app/config/testing目录下的database.php，定义了运行测试用例时的数据库环境；
app/tests目录下的TestCase.php文件中，增加setUp和prepareForTests方法，自动执行数据库迁移，以便把数据迁移到测试数据库中，特别这里定义的是内存数据库;

执行

    sudo chmod -R 755 app/storage

设置app/storage目录为可写;

执行

    sudo chmod 755 app/database，sudo chmod 755 app/database/production.sqlite

如果要使用sqlite数据库，则.sqlite后缀的数据库文件需要为可写，并且其所在的目录也需要为可写；



构建数据库结构:

创建users表，roles表，以及两者的多对多连接表

    php artisan generate:migration create_users_table --fields="username:string, password:string, user_no:string, first_name:string, middle_name:string, last_name:string, display_name:string, email:string, language:string, isproxy:boolean, parent_id:integer, user_rating:integer"

    php artisan generate:migration create_roles_table --fields="name:string(40), rights:string(10240), description:string(255), priority:integer:unsigned:default(0), is_system:boolean:default(1), right_settable:boolean:default(1), user_settable:boolean:default(1), disabled:boolean:unsigned:default(0), sequence:integer:unsigned:default(0)"

    php artisan generate:pivot roles users

然后执行如下命令,生成相应的3张表

    php artisan migrate

这里可以通过定制参数来指定执行相应路径下的多个或单个文件,参数说明通过输入命令:

    php artisan migrate --help

命令:

    php artisan migrate:rollback

回滚最近的一次迁移

命令:

    php artisan migrate:reset

回滚所有迁移, 相当于清空数据库

命令:

    php artisan migrate:refresh [--seed]

回滚所有迁移, 并重新运行一次所有迁移, [是否同时填充数据库]

构建测试数据填充

命令:

    php artisan generate:seed 表名

创建一个数据填充类

然后编写相应的填充数据, 或数据填充规则, 将新增的数据填充类加入DatabaseSeeder.php文件中

命令:

    php artisan db:seed

执行数据填充, 默认运行DatabaseSeeder.php类, 通过执行类似这样的语句: $this->call('UsersTableSeeder'); 调用其他seed类

命令:

    php artisan db:seed --class=UsersTableSeeder

也可以指定执行一个seed类来执行



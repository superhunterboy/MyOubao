/**
 * gulp
 * $ npm install linco.dir gulp-ruby-sass gulp-autoprefixer gulp-minify-css gulp-jshint gulp-concat gulp-concat-css gulp-uglify gulp-obfuscate gulp-imagemin gulp-notify gulp-rename gulp-livereload gulp-cache del --save-dev
 */

var gulp         = require('gulp'),
    sass         = require('gulp-ruby-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss    = require('gulp-minify-css'),
    jshint       = require('gulp-jshint'),
    uglify       = require('gulp-uglify'),
    obfuscate    = require('gulp-obfuscate'),
    imagemin     = require('gulp-imagemin'),
    rename       = require('gulp-rename'),
    concat       = require('gulp-concat'),
    concatCss    = require('gulp-concat-css'),
    notify       = require('gulp-notify'),
    cache        = require('gulp-cache'),
    livereload   = require('gulp-livereload'),
    del          = require('del'),
    spriter      = require('gulp-css-spriter'),
    dir          = require('linco.dir'); // https://github.com/gavinning/dir

// linco.dir用于遍历文件夹目录及子目录
// opt参数为可选，不设置将执行默认规则，以下为默认规则
var opt = {
    deep        : true,
    filterFile  : ['^.*', '.svn-base', '_tmp', '副本', 'desktop.ini', '.DS_Store'],
    filterFolder: ['^.git$', '^.svn$'],
    onlyFile    : [],
    onlyFolder  : []
}



gulp.task('default', [], function() {
    gulp.start(
            'scripts-base', 
            'scripts-game', 
            
            'scripts-game-ssc', 
            'scripts-game-l115',
            'scripts-game-3d',
            'scripts-game-p35',
            'scripts-game-k3',
            'scripts-game-pk10',

            'scripts-game-lucky28',

            'scripts-game-ssc-init',
            'scripts-game-n115-init',
            'scripts-game-3d-init',
            'scripts-game-p35-init',
            'scripts-game-k3-init',
            'scripts-game-pk10-init',
            'scripts-game-dice-init',
            'scripts-game-lhd-init',
            'scripts-game-sports-init',
            'scripts-game-bjl-init',

            'scripts-bak'
        );
    //gulp.start('styles', 'scripts-base', 'scripts-game');
    //gulp.start('watch');
});



gulp.task('watch', function() {
    // Watch .css files
    gulp.watch('assets/images/**/*.css', ['styles']);
    // Watch .js files
    gulp.watch('assets/js/*.js', ['scripts-base']);
    gulp.watch('assets/js/game/*.js', ['scripts-game']);
    // Watch image files
    // gulp.watch('src/images/**/*', ['images']);
});



gulp.task('clean', function(cb) {
    del(['dist/assets/css', 'dist/assets/js', 'dist/assets/img'], cb)
});



gulp.task('styles', function() {
    var obj = dir('assets/images/', {deep: true, onlyFile: ['*.css']});
    return gulp.src(obj.files) // assets/images/**/*.css
        //  .pipe(sass({ style: 'expanded' }))
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
        // .pipe(concatCss('all.css'))
        .pipe(gulp.dest('dist/assets/css'))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(minifycss())
        .pipe(gulp.dest('dist/assets/css'));
});


//js源码备份
gulp.task('scripts-bak', function() {
    return gulp.src(['assets/js/*.js', 'assets/js/*/*.js', 'assets/js/*/*/*.js'])
        .pipe(gulp.dest('../../jsbak-dayu'));
});




gulp.task('scripts-base', function() {
    var filelist = [
        'assets/js/jquery-1.9.1.js',
        'assets/js/jquery.easing.1.3.js',
        'assets/js/jquery.tmpl.js',
        'assets/js/jquery.mousewheel.js',
        'assets/js/jquery.cookie.js',
        'assets/js/jquery.jscrollpane.js',
        'assets/js/bomao.base.js',
        'assets/js/bomao.Tab.js',
        'assets/js/bomao.Slider.js',
        'assets/js/bomao.Hover.js',
        'assets/js/bomao.Select.js',
        'assets/js/bomao.Timer.js',
        'assets/js/bomao.Mask.js',
        'assets/js/bomao.MiniWindow.js',
        'assets/js/bomao.Tip.js',
        'assets/js/bomao.Message.js',
        'assets/js/bomao.DatePicker.js',
        'assets/js/bomao.Ernie.js',
        'assets/js/bomao.SliderBar.js',
        'assets/js/bomao.Alive.js',
        'assets/js/bomao.SideTip.js',
        'assets/js/bomao.Behavior.js',
        'assets/js/bomao.Encrypt.js',
        'assets/js/bomao.WebSocket.js',
        'assets/js/bomao.Keyboard.js',
        'assets/js/bomao.Voice.js'
    ];
    return gulp.src(filelist)
        .pipe(concat('base-all.js'))
        .pipe(gulp.dest('assets/js'))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js'));
});


gulp.task('scripts-game', function() {
    //var obj = dir('assets/js/game/', {deep: true, onlyFile: ['*.js']});
    var filelist = [
        'assets/js/game/bomao.Games.js',
        'assets/js/game/bomao.Game.js',
        'assets/js/game/bomao.GameMethod.js',
        'assets/js/game/bomao.GameMessage.js',
        'assets/js/game/bomao.GameTypes.js',
        'assets/js/game/bomao.GameStatistics.js',
        'assets/js/game/bomao.GameOrder.js',
        'assets/js/game/bomao.GameTrace.js',
        'assets/js/game/bomao.GameSubmit.js',
        'assets/js/game/bomao.GameRecords.js',

        'assets/js/game/bomao.TableGame.js',
        'assets/js/game/bomao.TableGame.Dice.js',
        'assets/js/game/bomao.TableGame.Area.js',
        'assets/js/game/bomao.TableGame.Chip.js',
        'assets/js/game/bomao.TableGame.Chips.js',
        'assets/js/game/bomao.TableGame.ChipsGroup.js',
        'assets/js/game/bomao.TableGame.ContextMenu.js',
        'assets/js/game/bomao.TableGame.BetHistory.js',
        'assets/js/game/bomao.TableGame.History.js',
        'assets/js/game/bomao.TableGame.Cup.js',
        'assets/js/game/bomao.TableGame.UserBalance.js',
        'assets/js/game/bomao.TableGame.LhdHistory.js',
        'assets/js/game/bomao.TableGame.Poker.js',
        'assets/js/game/bomao.TableGame.PokerManager.js',
        'assets/js/game/bomao.TableGame.Lhd.js',
        

        'assets/js/game/ssc/bomao.Games.SSC.js',
        'assets/js/game/ssc/bomao.Games.SSC.Danshi.js',
        'assets/js/game/ssc/bomao.Games.SSC.Message.js',
        'assets/js/game/l115/bomao.Games.L115.js',
        'assets/js/game/l115/bomao.Games.L115.Danshi.js',
        'assets/js/game/l115/bomao.Games.L115.Message.js',
        'assets/js/game/3d/bomao.Games.3D.js',
        'assets/js/game/3d/bomao.Games.3D.Danshi.js',
        'assets/js/game/3d/bomao.Games.3D.Message.js',
        'assets/js/game/plw/bomao.Games.P35.js',
        'assets/js/game/plw/bomao.Games.P35.Danshi.js',
        'assets/js/game/plw/bomao.Games.P35.Message.js',
        'assets/js/game/k3/bomao.Games.K3.js',
        'assets/js/game/k3/bomao.Games.K3.Danshi.js',
        'assets/js/game/k3/bomao.Games.K3.Message.js',
        'assets/js/game/pk10/bomao.Games.PK10.js',
        'assets/js/game/pk10/bomao.Games.PK10.Danshi.js',
        'assets/js/game/pk10/bomao.Games.PK10.Message.js',

        'assets/js/game/bomao.SportsGame.js',
        'assets/js/game/bomao.TableGame.Bjl.js',
        'assets/js/game/bomao.TableGame.BjlPoker.js',
        'assets/js/game/bomao.TableGame.BjlPokerManager.js',
        'assets/js/game/bomao.TableGame.BjlHistory.js',



    ];
    return gulp.src(filelist) // 'assets/js/game/**/*.js'
        //  .pipe(jshint('.jshintrc'))
        // .pipe(jshint.reporter('default'))
        .pipe(concat('game-all.js'))
        .pipe(gulp.dest('assets/js/game'))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});



//时时彩玩法文件
gulp.task('scripts-game-ssc', function() {
    return gulp.src('assets/js/game/ssc/*.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game/ssc'));
});


//11选5玩法文件
gulp.task('scripts-game-l115', function() {
    return gulp.src('assets/js/game/l115/*.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game/l115'));
});


//3D玩法文件
gulp.task('scripts-game-3d', function() {
    return gulp.src('assets/js/game/3d/*.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game/3d'));
});

//P3P5玩法文件
gulp.task('scripts-game-p35', function() {
    return gulp.src('assets/js/game/plw/*.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game/plw'));
});


//K3玩法文件
gulp.task('scripts-game-k3', function() {
    return gulp.src('assets/js/game/k3/*.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game/k3'));
});

//pk10玩法文件
gulp.task('scripts-game-pk10', function() {
    return gulp.src('assets/js/game/pk10/*.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game/pk10'));
});


//时时彩初始化文件
gulp.task('scripts-game-ssc-init', function() {
    return gulp.src('assets/js/game/game-ssc-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});
//11选5初始化文件
gulp.task('scripts-game-n115-init', function() {
    return gulp.src('assets/js/game/game-n115-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});
//3D初始化文件
gulp.task('scripts-game-3d-init', function() {
    return gulp.src('assets/js/game/game-3d-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});
//P3P5初始化文件
gulp.task('scripts-game-p35-init', function() {
    return gulp.src('assets/js/game/game-p35-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});

//K3初始化文件
gulp.task('scripts-game-k3-init', function() {
    return gulp.src('assets/js/game/game-k3-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});

//pk10初始化文件
gulp.task('scripts-game-pk10-init', function() {
    return gulp.src('assets/js/game/game-pk10-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});


//晒宝初始化文件
gulp.task('scripts-game-dice-init', function() {
    return gulp.src('assets/js/game/game-dice-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});


//龙虎斗初始化文件
gulp.task('scripts-game-lhd-init', function() {
    return gulp.src('assets/js/game/game-lhd-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});


//百家乐
gulp.task('scripts-game-bjl-init', function() {
    return gulp.src('assets/js/game/game-bjl-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});

//竞彩初始化文件
gulp.task('scripts-game-sports-init', function() {
    return gulp.src('assets/js/game/game-sports-init.js')
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});
//init文件压缩
gulp.task('a1', ['scripts-game-ssc-init','scripts-game-n115-init','scripts-game-3d-init','scripts-game-p35-init','scripts-game-k3-init','scripts-game-pk10-init']);
//快乐28
gulp.task('scripts-game-lucky28', function() {
    //var obj = dir('assets/js/game/', {deep: true, onlyFile: ['*.js']});
    var filelist = [
        'assets/js/moment.min.js',
        'assets/js/bootstrap.min.js',
        'assets/js/daterangepicker.js',
        'assets/js/game/lucky28/bomao.Lucky28.js',
        'assets/js/game/lucky28/bomao.Lucky28.GameBase.js',
        'assets/js/game/lucky28/bomao.Lucky28.Game.js',
        'assets/js/game/lucky28/bomao.Lucky28.DataService.js',
        'assets/js/game/lucky28/bomao.Lucky28.prizePeriod.js',
        'assets/js/game/lucky28/bomao.Lucky28.zuhe.js',
        'assets/js/game/lucky28/bomao.Lucky28.hezhi.js',
        'assets/js/game/lucky28/bomao.Lucky28.informationTimer.js',
        'assets/js/game/lucky28/bomao.Lucky28.informationWait.js',
        'assets/js/game/lucky28/bomao.Lucky28.informationResult.js',
        'assets/js/game/lucky28/bomao.Lucky28.informationSuspension.js',
        'assets/js/game/lucky28/bomao.Lucky28.orderWindow.js',
        'assets/js/game/lucky28/bomao.Lucky28.miniHistory.js',
        'assets/js/game/lucky28/bomao.Lucky28.order.js',
        'assets/js/game/lucky28/bomao.Lucky28.clock.js',
        'assets/js/game/lucky28/bomao.Lucky28.resultRecord.js',
        'assets/js/game/lucky28/bomao.Lucky28.historyRecord.js',
        'assets/js/game/lucky28/bomao.Lucky28.Awardforlottery.js',
        'assets/js/game/game-lucky28-init.js',
    ];
    return gulp.src(filelist)
        .pipe(concat('game-lucky28-all.js'))
        .pipe(gulp.dest('assets/js/game'))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/assets/js/game'));
});






gulp.task('spriter', function() {
    //需要自动合并雪碧图的样式文件
    return gulp.src('./assets/images/indexClient/css/*.css')
        .pipe(spriter({
            // 生成的spriter的位置
            'spriteSheet': './assets/images/indexClient/icon/all.png',
            // 生成样式文件图片引用地址的路径
            // 如下将生产：backgound:url(../images/sprite20324232.png)
            'pathToSpriteSheetFromCSS': '../icon/all.png'
        }))
        //产出路径
        .pipe(gulp.dest('./assets/images/indexClient/css/dist/'));
});


gulp.task('cssmin', function () {
    gulp.src('./assets/images/indexClient/css/*.css')
        .pipe(minifycss({
            advanced: false,//类型：Boolean 默认：true [是否开启高级优化（合并选择器等）]
            compatibility: 'ie7',//保留ie7及以下兼容写法 类型：String 默认：''or'*' [启用兼容模式； 'ie7'：IE7兼容模式，'ie8'：IE8兼容模式，'*'：IE9+兼容模式]
            keepBreaks: true,//类型：Boolean 默认：false [是否保留换行]
            keepSpecialComments: '*'
            //保留所有特殊前缀 当你用autoprefixer生成的浏览器前缀，如果不加这个参数，有可能将会删除你的部分前缀
        }))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./assets/images/indexClient/css/dist'));
});
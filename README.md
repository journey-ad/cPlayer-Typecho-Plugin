# cPlayer-Typecho-Plugin
A typecho plugin for the beautiful html5 music player https://github.com/Copay/cPlayer

[Demo](https://imjad.cn/archives/none/cplayer-test)

![demo](https://img.imjad.cn/images/2017/01/15/Screenshotfrom2017-01-1513-45-40.png)
## 介绍
1. 通过简短的代码在文章或页面中插入漂亮的Html5播放器
2. 自动解析lrc链接，可根据歌曲名和歌手名自动查找封面并生成缓存
3. 支持网易云音乐单曲、歌单、专辑、歌手id的解析
4. 网易云音乐返回结果支持https
5. 与cPlayer保持同步更新

## 声明
本插件仅供个人学习研究使用，请勿将其用作商业用途，音乐版权归网易云音乐 music.163.com 所有。

## 安装方法
安装前请确保cache目录可写（保存缓存用，否则会让博客加载缓慢）

主机需支持curl扩展，否则将可能不能自动查找封面、解析网易云音乐id、从https的url中获取歌词(file_get_contents在不支持openssl的主机中不能打开https链接)

Download ZIP, 解压，将 cPlayer-Typecho-Plugin-master 重命名为 cPlayer ，之后上传到你博客中的 /usr/plugins 目录，在后台启用即可

## 使用方法
在文章或页面中加入下方格式的短代码即可：

#### 调用格式

##### 单曲播放：
```
[player 属性1="值1" 属性2="值2" 属性3="值3" /]
or
[player 属性1="值1" 属性2="值2" 属性3="值3"][lrc]歌词[/lrc][tlrc]歌词翻译[/tlrc][/player]
```

example:
```
[player url="http://xxx.com/xxx.mp3" artist="Someone" name="Title"/]

[player url="http://xxx.com/xxx.mp3" artist="Someone" name="Title"][lrc][00:00.00]Test lyrics[/lrc][tlrc][00:00.00]Test lyrics[/tlrc][/player]

网易云音乐：
[player id="26598946"/]

```

##### 多首歌曲：

```
[player 属性1="值1" 属性2="值2" 属性3="值3"]
[mp3 歌曲属性1="值1" 歌曲属性2="值2" 歌曲属性3="值3"/]
[mp3 歌曲属性1="值1" 歌曲属性2="值2" 歌曲属性3="值3"][lrc]歌词[/lrc][tlrc]歌词翻译[/tlrc][/mp3]
[/player]
```

example:
```
[player]
[mp3 url="http://xxx.com/xxx.mp3" artist="Someone" name="Title"/]
[mp3 url="http://xxx.com/xxx.mp3" artist="Someone" name="Title"][lrc][00:00.00]Test lyrics[/lrc][tlrc][00:00.00]Test lyrics[/tlrc][/mp3]
[mp3 id="29947420"/] //网易云音乐歌曲id直接解析
[/player]
```

##### 网易云音乐解析示例：
```
[player id='36492783,33715196,461011'/] //一次加入三首歌
[player id='456390601' type='collect'/] //歌单
[player id='2116' type='artist'/] //艺人热门五十首
[player id='2897014' type='album'/] //专辑
[player type='recommend'/] //每日推荐
```

如果要阻止代码解析成为播放器的话，用[]包裹[player]标签即可

```
[[player id='36492783,33715196,461011'/]]

输出：
[player id='36492783,33715196,461011'/]
```

#### 用到的shortcode标签
```
[player] :整个播放器的标签，里面可用下面提到的所有属性
[mp3] :可以用歌曲属性和网易云音乐属性，用于嵌套在[player]标签内部添加音乐
[lrc] :用以添加文本的歌词，可嵌套在[mp3],[player]标签内部；只有当其父标签只定义一首歌的时候才起作用
[tlrc] :用以添加文本的歌词翻译，可嵌套在[mp3],[player]标签内部；只有当其父标签只定义一首歌的时候才起作用，需要[lrc]标签
```

#### 关于各个标签的属性
歌曲的属性(可在[mp3]或[player]中使用，不能用于修改整个歌单的属性)：
```
url: mp3文件的链接，必需
lrc: 歌词的lrc链接，非必需
tlrc: 歌词翻译的lrc链接，需要lrc，非必需
lrcoffset: 歌词整体提前时间（ms）若这个值为负数则为歌词整体延后的时间
name: 歌曲的标题，若值为空则显示 Unknown
artist: 歌曲的艺术家，若值为空则显示 Unknown
cover: 封面图片链接，非必需，若该值为图片链接则按照链接加载封面图，若没有此属性则会按照name和artist自动从豆瓣api中查找封面图，若值为 false 则不自动查找封面，显示默认封面图片
```
网易云音乐(与歌曲属性用法一样)
```
id: 歌曲/歌单/专辑/艺人的id，如果是歌曲的话可用，分隔歌曲id一次插入多首歌曲
type: 用以判断id的类型，分为4种：song:歌曲,album:专辑,artist:艺人,collect:歌单,recommend:每日推荐
```

#### 每日推荐说明
需先在设置页填入网易云音乐 Cookie 中 MUSIC\_U 字段的值，需要带上 MUSIC\_U=，日推缓存将在缓存时间第二天 6:00 之后失效

#### 清空歌词，播放列表、封面图片url的缓存

前往插件设置页面点击红色清空缓存按钮即可

## Thanks

制作过程中参考~~(基本照抄)~~了[zgq354](https://github.com/zgq354/APlayer-Typecho-Plugin)的代码，特此感谢

## LICENSE

MIT © [journey.ad](https://github.com/journey-ad/)

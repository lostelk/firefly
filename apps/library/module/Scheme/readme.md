## scheme 说明

数据的入参，和返回结果的结构直接影响到了程序的安全性和稳定性

### 数据标准化

$room = \Common\Core\Scheme::out($room, \Common\Scheme\RoomScheme::class);

### rule 入参控制

文件定义在 rule 文件夹内

命名规则是按url的path

比如：http://quanmin.tv/gift/add?abc=1

命名为 `GiftAdd.php`

内容为：

````
return [
    'name' => 'required|string',
    'number' => 'required|int',
    'pic' => 'string',
];
````

````
    $demo = [
        'id'       => '123200232', // 目标：需要转化成int
        'name'     => '你好',
        'livekey'  => 'jdslolloowww',
        'dateline' => '1478227137', //目标：需要转化成datetime
        'password' => '敏感数据',    //目标：过滤该字段
        //'stream' => ''            //字段不存在，目标：生成该字段
    ];
    $data = Scheme::qmOut($demo, 'SchemeDemo', ['password']);

    return $this->success(['demo' => $data]);

````

详细看 DemoScheme.php

命名规则按对象，比如房间，RoomScheme
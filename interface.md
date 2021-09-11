### 1 修改允许上传的文件类型
    curl -X POST http://127.0.0.1:8001/ocs/v2.php/cloud/admin/set_file_type -d 'fileTypes=exe,txt,jpeg'

### 2 获取登陆IP 

    curl -X GET  'http://127.0.0.1:8001/ocs/v2.php/cloud/admin/login_log?userId=Sysadmin@2021&page=0&limit=10'

### 3 设置密码有效期

    curl -X POST 'http://127.0.0.1:8001/ocs/v2.php/cloud/admin/set_password_life'
    -d '{"day":4}'

### 4 设置文件等级

    curl -X POST http://127.0.0.1:8001/ocs/v2.php/apps/files_sharing/api/v1/set_level
        -d 'level=秘密&path=/1234.jpg'

### 5 获取操作日志

    curl -X GET http://127.0.0.1:8001/ocs/v2.php/cloud/admin/list_log?userId=Sysadmin@2021&page=0&limit=50

        userId 操作对象日志，默认为空
        page 分页
        limit 单页数据条数

    rsp 
    {
        "ocs": {
            "meta": {
                "status": "ok",
                "statuscode": 200,
                "message": "OK"
            },
            "data": [
                {
                    "id": "21",
                    "uid": "Sysadmin@2021",
                    "ip": "127.0.0.1",
                    "log": "登陆",
                    "time": "1630422063"
                }
            ]
        }
    }

### 6 设置登录配置信息

    curl -X POST http://127.0.0.1:8001/ocs/v2.php/cloud/admin/set_login_conf -d 'retryTimes=3&interval=300'

        说明 retryTimes 最大重试次数，interval 达到最大重次数后，隔interval后可重新登陆

### 7 用户日志 http://127.0.0.1:8001/ocs/v2.php/cloud/admin/search_log?userId=Sysadmin@2021&page=0&limit=50&filter=用户行为&end=1631325811

    说明 userId 用户id;page 页数; limit 每页数量; start 开始时间; end 结束时间； filter 过滤条件 ： 登陆、登出、IP地址、用户行为

    {
        "ocs": {
            "meta": {
                "status": "ok",
                "statuscode": 200,
                "message": "OK"
            },
            "data": [
                {
                    "id": "1",
                    "uid": "Sysadmin@2021",
                    "ip": "127.0.0.1",
                    "log": "添加了用户canghai123",
                    "time": "1631325012"
                }
            ]
        }
    }
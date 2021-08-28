### 1 修改允许上传的文件类型
    curl -X POST http://127.0.0.1:8001/ocs/v2.php/cloud/admin/set_file_type -d 'fileTypes=exe,txt,jpeg'

### 2 获取登陆IP 

    curl -X GET  'http://127.0.0.1:8001/ocs/v2.php/cloud/admin/login_log?userId=Sysadmin@2021&page=0&limit=10'

### 3 设置密码有效期

    curl -X POST 'http://127.0.0.1:8001/ocs/v2.php/cloud/admin/set_password_life'
    -d '{"day":4}'


window.addEventListener('load', function () {
    var timer = setInterval(function () {
        var box = $("#recommendations").parent();
        if (box.length) {
            box.append("当前为管理员身份，只能做相应管理员的配置。");
            clearInterval(timer);
        }
    }, 1000)
})

/**
 * 推荐位添加
 * @author WeiZeng
 */

// 表单验证
layui.form().verify({
    // 推荐位名称
    position_name: function(value) {
        if(value.length > 60) {
            return '推荐位名称长度在20个字符及以内';
        }
    },
    // 推荐位描述
    description: function(value) {
        if(value.length > 150) {
            return '推荐位描述长度在150个字符及以内';
        }
    }
});

// 添加
layui.form().on('submit(addBtn)', function(data) {
    // 发送请求
    $.ajax({
        url: Common.positionAdd,
        type: 'POST',
        dataType: 'JSON',
        data: data.field,
        success: function(result) {
            if(result['status'] === 1) {
                layer.confirm(result['message'], {
                    title: '成功提示',
                    icon: 1,
                    btn: ['继续添加', '转到列表']
                }, function() {
                    window.location.reload();
                }, function() {
                    window.location.href = Common.position;
                });
            }
            if(result['status'] === 0) {
                layer.alert(result['message'], {
                    title: '错误提示',
                    icon: 2
                });
            }
        }
    });

    // 禁止表单提交
    return false;
});

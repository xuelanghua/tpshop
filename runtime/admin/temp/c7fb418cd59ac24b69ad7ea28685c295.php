<?php /*a:1:{s:64:"E:\project\tpshop\app\admin\view\setting\system_config\index.php";i:1567990148;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=$form->getTitle()?></title>

    <link href="/system/frame/css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="/system/css/layui-admin.css" rel="stylesheet">
    <link href="/system/frame/css/style.min.css?v=3.0.0" rel="stylesheet">
    <link href="/system/frame/css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
    <script src="/static/plug/vue/dist/vue.min.js"></script>
    <link href="/static/plug/iview/dist/styles/iview.css" rel="stylesheet">
    <script src="/static/plug/iview/dist/iview.min.js"></script>
    <script src="/static/plug/jquery/jquery.min.js"></script>
    <script src="/static/plug/form-create/province_city.js"></script>
    <script src="/static/plug/form-create/form-create.min.js"></script>
    <link href="/static/plug/layui/css/layui.css" rel="stylesheet">
    <script src="/static/plug/layui/layui.all.js"></script>
    <style>
        /*弹框样式修改*/
        .ivu-modal{top: 20px;}
        .ivu-modal .ivu-modal-body{padding: 10px;}
        .ivu-modal .ivu-modal-body .ivu-modal-confirm-head{padding:0 0 10px 0;}
        .ivu-modal .ivu-modal-body .ivu-modal-confirm-footer{display: none;padding-bottom: 10px;}
        .ivu-date-picker {display: inline-block;line-height: normal;width: 280px;}
        .ivu-modal-footer{display: none;}
        .ivu-poptip-popper{text-align: left;}
        .ivu-icon{padding-left: 5px;}
        .ivu-btn-long{width: 10%;min-width:100px;margin-left: 18%;}
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <?php if($config_tab == null): ?>
                <div class="ibox-title">
                    <h5>系统配置</h5>
                </div>
                <?php else: ?>
                <div class="tabs-container ibox-title">
                    <ul class="nav nav-tabs">
                        <?php if(is_array($config_tab) || $config_tab instanceof \think\Collection || $config_tab instanceof \think\Paginator): $i = 0; $__LIST__ = $config_tab;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['value'] == $tab_id): ?>
                        <li class="active"><a href="<?php echo Url('index',array('tab_id'=>$vo['value'],'type'=>$vo['type'])); ?>"><i class="fa fa-<?php echo htmlentities($vo['icon']); ?>"></i><?php echo htmlentities($vo['label']); ?></a></li>
                        <?php else: ?>
                        <li><a href="<?php echo Url('index',array('tab_id'=>$vo['value'],'type'=>$vo['type'])); ?>"><i class="fa fa-<?php echo htmlentities($vo['icon']); ?>"></i><?php echo htmlentities($vo['label']); ?></a></li>
                        <?php endif; ?>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                    <?php endif; ?>

                    <div class="ibox-content">
                        <div class="p-m m-t-sm" id="configboay">

                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
</body>

<script>
    formCreate.formSuccess = function(form,$r){
        <?=$form->getSuccessScript()?>
        $f.btn.loading(false)();
    };

    (function () {
        var create = (function () {
            var getRule = function () {
                var rule = <?=json_encode($form->getRules())?>;
                rule.forEach(function (c) {
                    if ((c.type == 'cascader' || c.type == 'tree') && Object.prototype.toString.call(c.props.data) == '[object String]') {
                        if (c.props.data.indexOf('js.') === 0) {
                            c.props.data = window[c.props.data.replace('js.', '')];
                        }
                    }
                });
                return rule;
            }, vm = new Vue,name = 'formBuilderExec<?= !$form->getId() ? '' : '_'.$form->getId() ?>';
            var _b = false;
            window[name] =  function create(el, callback) {
                if(_b) return ;
                _b = true;
                if (!el) el = document.getElementById('configboay');
                var $f = formCreate.create(getRule(), {
                    el: el,
                    form:<?=json_encode($form->getConfig('form'))?>,
                    row:<?=json_encode($form->getConfig('row'))?>,
                    submitBtn:<?=$form->isSubmitBtn() ? '{}' : 'false'?>,
                    resetBtn:<?=$form->isResetBtn() ? 'true' : '{}'?>,
                    iframeHelper:true,
                    global:{
                        upload: {
                            props:{
                                onExceededSize: function (file) {
                                    vm.$Message.error(file.name + '超出指定大小限制');
                                },
                                onFormatError: function () {
                                    vm.$Message.error(file.name + '格式验证失败');
                                },
                                onError: function (error) {
                                    vm.$Message.error(file.name + '上传失败,(' + error + ')');
                                },
                                onSuccess: function (res, file) {
                                    if (res.code == 200) {
                                        file.url = res.data.filePath;
                                    } else {
                                        vm.$Message.error(res.msg);
                                    }
                                },
                            },
                        },
                    },
                    //表单提交事件
                    onSubmit: function (formData) {
                        $f.btn.loading(true);
                        $.ajax({
                            url: '<?=$form->getAction()?>',
                            type: '<?=$form->getMethod()?>',
                            dataType: 'json',
                            data: formData,
                            success: function (res) {
                                if (res.code == 200) {
                                    vm.$Message.success(res.msg);
                                    $f.btn.loading(false);
                                    formCreate.formSuccess && formCreate.formSuccess(res, $f, formData);
                                    callback && callback(0, res, $f, formData);
                                    //TODO 表单提交成功!
                                } else {
                                    vm.$Message.error(res.msg || '表单提交失败');
                                    $f.btn.loading(false);
                                    callback && callback(1, res, $f, formData);
                                    //TODO 表单提交失败
                                }
                            },
                            error: function () {
                                vm.$Message.error('表单提交失败');
                                $f.btn.loading(false);
                            }
                        });
                    }
                });
                return $f;
            };
            return window[name];
        }());
        window.$f = create();
    })();
</script>
</html>
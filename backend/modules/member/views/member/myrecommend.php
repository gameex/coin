<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '用户信息';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$starttime = '';
$endtime = '';
if (!empty($_GET['starttime'])) {
    $starttime = $_GET['starttime'];
}
if (!empty($_GET['endtime'])) {
    $endtime = $_GET['endtime'];
}
$type=1;
$keyword=1;
?>
<block name="style" >
    <style type="text/css">
        .clearfix,.builder-container{
            margin-left:30px; 
        }
        .tree {
            min-height:400px;
        }
        .tree li {
            list-style-type:none;
            margin:0;
            padding:10px 5px 0 5px;
            position:relative
        }
        .tree li::before, .tree li::after {
            content:'';
            left:-20px;
            position:absolute;
            right:auto
        }
        .tree li::before {
            border-left:1px solid #999;
            bottom:50px;
            height:100%;
            top:0;
            width:1px
        }
        .tree li::after {
            border-top:1px solid #999;
            height:20px;
            top:25px;
            width:25px
        }
        .tree li span {
            -moz-border-radius:5px;
            -webkit-border-radius:5px;
            border:1px solid #999;
            border-radius:5px;
            display:inline-block;
            padding:3px 8px;
            text-decoration:none
        }
        .tree li.parent_li>span {
            cursor:pointer
        }
        .tree>ul>li::before, .tree>ul>li::after {
            border:0
        }
        .tree li:last-child::before {
            height:30px
        }
        .tree li.parent_li>span:hover, .tree li.parent_li>span:hover+ul li span {
            background:#eee;
            border:1px solid #94a0b4;
            color:#000
        }
        .tree > ul> li{
            display: block !important;
        }
        .blue{
            color: #3fa9f5;
            font-weight: 900;
        }
        input[tpye='text']{margin:0}
    </style>
</block>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>用户信息</h5>
                </div>
                <div class="ibox-content">
                    <div class="tree">
                        <?php  echo $tree; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
      $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', '折叠');
      $('.tree li.parent_li > span').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
          children.hide('fast');
          $(this).attr('title', '展开').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
          children.show('fast');
          $(this).attr('title', '折叠').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
      });
    });
</script>

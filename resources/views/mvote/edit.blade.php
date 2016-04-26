@extends('layouts.app')
<style type="text/css">
.selected{background:#5cb85c;}
#member .col-md-3{line-height: 30px;height:30px;margin-bottom:5px;text-align:center;}
#addQ{margin:13px 0;}
</style>
@section('content')
<div class="container">
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-10 col-md-offset-1">
            <form action="{{ url('manage/vote/update',['id'=>$vote->id]) }}" method="post" id="voteForm">
                 <input type="hidden" name="_method" value="PUT">
                <div class="form-group">
                    <label for="title"></label><input type="text" class="form-control" placeholder="输入调研标题" id="title" name="title" value="{{ $vote->title }}">
                </div>
                <div class="form-group">
                    <label for="info"></label>
                    <textarea class="form-control" placeholder="输入调研的说明" name="info" id="info">{{ $vote->info }}</textarea>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        创建调研内容
                    </div>
                    <div class="panel-body">
                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-8 text-center">
                                调研评测项目
                            </div>
                            <div class="col-md-2 text-center">
                                占百分比<a href="javascript:;" class="btn btn-success btn-xs" style="margin-left:5px;" id="average">平均</a>
                            </div>
                            <div class="col-md-2 text-center">
                                操作
                            </div>
                        </div>
                        <div id="questions">
                            <?php foreach( $vote->getNode as $node): ?>
                            <div class="form-group question">
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="text" name="q[]" id="" class="form-control" placeholder="输入调研选项" value="{{ $node->title }}">
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <input type="text" name="p[]" id="" class="form-control percent" placeholder="百分比" value="{{ $node->percent }}">
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <a href="javascript:;" class="btn btn-danger" onclick="delQ(this)">删除</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>                        
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1"><a href="javascript:;" class="btn btn-block btn-info" id="addQ">增加调研项</a></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                <label for="">调研时间</label>
                    <div class="input-group input-daterange">
                        <input type="text" class="form-control" name="starttime" value="{{ date('Y-m-d',$vote->starttime) }}">
                        <span class="input-group-addon">到</span>
                        <input type="text" class="form-control" name="endtime" value="{{ date('Y-m-d',$vote->endtime) }}">
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        选择被评测人
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="group"></div>
                            </div>
                            <div class="col-md-6">
                                <div id="member"></div>
                            </div>
                        </div>            
                    </div>
                </div>
                <input type="hidden" name="type" value="1">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="formember" value="">
                <div class="form-group">
                    <input type="submit" value="创建" class="btn btn-block btn-primary" id="confirmBtn">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="{{ asset('style/css/bootstrap-treeview.min.css') }}">
<link rel="stylesheet" href="{{ asset('style/css/bootstrap-datepicker.min.css') }}">
<script type="text/javascript" src="{{ asset('style/js/bootstrap-treeview.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('style/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('style/js/locales/bootstrap-datepicker.zh-CN.min.js') }}"></script>
<script type="text/javascript">
    var group,member;
    var tree = {};
    var selectedMembers = {!! json_encode($selectedMembers) !!};
    $(function(){

        function buildGrouTree(){
            $.ajax({
                url:"{{ url('manage/contact/glist') }}",
                type: 'get',
                dataType: 'json',
                success: function(data){
                    group = data.rs;
                    tree = group;
                    for(i in group ){
                        tree[i].text = group[i].name;
                        tree[i].href="{{ url('manage/contact/ulist') }}";
                        tree[i].nodes = [];
                        for(j in group){
                            if( group[j].pid == group[i].group_id){
                                group[j].text = group[j].name;
                                tree[i].nodes.push(group[j]);
                            }
                        }
                    }
                    $("#group").treeview({
                        onNodeSelected: function(event,data){
                            buildMember(data.group_id);
                        },
                        data:[tree[0]]
                    });
                },
                error: function(){
                    console.log('network err');
                }

            });
        }

        function buildMember(id){
            $.ajax({
                url: "{{ url('manage/contact/ulist/') }}/"+id,
                type:'get',
                typeData:'json',
                data:{},
                success: function(data){
                    members = data.rs;
                    $("#member").html("");
                    for(i in members){
                        if( selectedMembers.hasOwnProperty(members[i].userid) ){
                            style = 'selected';
                        }else{
                            style = '';
                        }
                        $("#member").append('<div class="col-md-3 '+style+'" data-userid="'+members[i].userid+'" data-department="'+members[i].department+'" data-name="'+members[i].name+'" data-position="'+members[i].position+'" style="cursor:pointer" onclick="memberClick(this)">'+members[i].name+'</div>');
                    };
                }
            });
        }

        buildGrouTree();
        var $qClone = $(".question:eq(0)").html();
        $("#addQ").on('click',function(){
            $("#questions").append($($qClone));
        });

        $("#average").on('click',function(){
            var l = $(".percent").length;
            var a = 100 / l;
            $('.percent').val(a.toFixed(2));
        });

        $("#voteForm").submit(function(){
            if( JSON.stringify(selectedMembers) == "{}" ){
                // notice 
                alert('未选被调研对象');
                return false;
            }
            var t = 0;
            var flag = true;

            $(".percent").each(function(){
                if( $.trim($(this).val()) == "" || isNaN($(this).val()) ){
                    flag = false;
                }else{
                    t = t + parseFloat($(this).val());
                }
            });

            if( Math.round(t) != 100 || ! flag ){
                alert('比例不正确');
                return false;
            }
            $("input[name=formember]").val(JSON.stringify(selectedMembers));
            // return false;
        });

        $('.input-daterange input').each(function() {
          $(this).datepicker({language:'zh-CN',format:"yyyy-mm-dd"});
        });
    });
    function memberClick(t){
        userid = $(t).data('userid');
        department = $(t).data('department');
        name = $(t).data('name');
        position = $(t).data('position');

        if( $(t).hasClass('selected') ){
            $(t).removeClass('selected');
            delete selectedMembers[userid];
        }else{
            $(t).addClass('selected');
            selectedMembers[userid] = {
                department:department,
                name:name,
                position:position
            };
        }
    }

    function delQ(t){
        if( $(t).parent().parent().parent().siblings().length == 0 ){
            return;
        }
        $(t).parent().parent().parent().remove();
    }
</script>
@endsection

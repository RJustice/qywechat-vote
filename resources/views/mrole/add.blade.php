@extends('layouts.app')
<style type="text/css">
a,a:active,a:hover,a:focus{display: block;text-align: center;margin:0 auto;color:#fff;font-weight: 400;text-decoration:none;cursor: pointer;font-size:15px;}
#member .col-md-3{line-height: 30px;height:30px;margin-bottom:5px;text-align:center;}
#addQ{margin:13px 0;}
#selectedrole{margin-bottom: 20px;}
#selectedrole a,.selected a{background-color:#46b8da;display: block;text-align: center;margin:0 auto;color:#fff;font-weight: 400;text-decoration:none;cursor: pointer;font-size:15px;}
#selectedrole div{margin-top:5px;}
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">已选择</div>
                <div class="panel-body">                    
                    <div id="selectedrole" class="row">
                        <?php foreach($users as $userid=>$user): ?>
                        <div onclick="memberClick(this)" style="cursor:pointer" data-position="" data-name="{{ $user['name'] }}" data-department="{{ $user['department'] }}" data-userid="{{ $userid }}" class="col-md-3 selected"><a>{{ $user['name'] }}</a></div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row m20">
        <div class="col-md-10 col-md-offset-1">
            <form action="{{ url('manage/role/update') }}" method="post" id="roleform">
                <input type="hidden" name="_method" value="PUT">
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-block">保存</button>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        选择微信可查看权限人员
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
                <input type="hidden" name="formember" value="">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-success btn-block">保存</button>
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
    // var selectedMembers = {};
    var selectedMembers = {!! empty($users)?'{}' : json_encode($users) !!};

    function memberClick(t){
        userid = $(t).data('userid');
        department = $(t).data('department');
        name = $(t).data('name');
        position = $(t).data('position');

        if( $(t).hasClass('selected') ){
            $(t).removeClass('selected');
            $("div[data-userid="+userid+"]").removeClass('selected');
            $("#selectedrole div[data-userid="+userid+"]").remove();
            delete selectedMembers[userid];
        }else{
            $(t).addClass('selected');
            $("div[data-userid="+userid+"]").addClass('selected');
            $("#selectediv[data-userid="+userid+"]").addClass('selected');
            $("#selectedrole").append('<div class="col-md-3 selected" data-userid="'+userid+'" data-department="'+department+'" data-name="'+name+'" data-position="'+position+'" style="cursor:pointer" onclick="memberClick(this)"><a>'+name+'</a></div>');
            selectedMembers[userid] = {
                department:department,
                name:name,
                position:position
            };
        }
        // console.log(selectedMembers);
    }
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
                        $("#member").append('<div class="col-md-3 '+style+'" data-userid="'+members[i].userid+'" data-department="'+members[i].department+'" data-name="'+members[i].name+'" data-position="'+members[i].position+'" style="cursor:pointer" onclick="memberClick(this)"><a>'+members[i].name+'</a></div>');
                    };
                }
            });
        }

        buildGrouTree();
        var $qClone = $("#questions").html();
        $("#addQ").on('click',function(){
            $("#questions").append($($qClone));
        });

        $("#average").on('click',function(){
            var l = $(".percent").length;
            var a = 100 / l;
            $('.percent').val(a.toFixed(2));
        });

        $("#roleform").submit(function(){
            $("input[name=formember]").val(JSON.stringify(selectedMembers));
            // return false;
        });

        $('.input-daterange input').each(function() {
          $(this).datepicker({language:'zh-CN',format:"yyyy-mm-dd"});
        });
    });

    function delQ(t){
        if( $(t).parent().parent().parent().siblings().length == 0 ){
            return;
        }
        $(t).parent().parent().parent().remove();
    }
</script>
@endsection
class PageManager{
    constructor(params) {

        this.naviURL={
            "home":"#",
            "user":"user.html",
            "privilege":"#",
            "role":"role.html",
            "userrole":"userrole.html",
            "log":"log.html",
            "login":"login.html",
            "logout":"logout.html",
        };


        this.pagePathPrefix="../";

    }//-/

    getTopNaviHtml(){
    /*
    <li><a href="${ this.naviURL.home}" target="_top">首页</a></li>
    <li><a href="${ this.naviURL.privilege}"  target="_top">权限</a></li>
        <li><a href="${ this.naviURL.role}"  target="_top">角色</a></li>*/
        let htmlStr=`
            <li><a href="${ this.naviURL.user}"  target="_top">用户</a></li>
            <li><a href="${ this.naviURL.role}"  target="_top">角色</a></li>
            <li><a href="${ this.naviURL.userrole}"  target="_top">用户角色分配</a></li>
            <li><a href="${ this.naviURL.log}"  target="_top">日志</a></li>
            <li><a href="${ this.naviURL.logout}"  target="_top"><i class="glyphicon glyphicon-off"></i> 退出</a></li>`;

        return htmlStr;
    }//-/

    getLeftNaviHtml(curChannelId){
        let htmlStr=`
        <ul class="nav nav-sidebar">
            <!--<li ><a href="#">首页 <span class="sr-only">(current)</span></a></li>-->
        <li class="${curChannelId=='user'? 'active':''}"><a href="${ this.naviURL.user}"><i class="glyphicon glyphicon-user"></i> 用户管理</a></li>            
        <li class="${curChannelId=='role'? 'active':''}"><a href="${ this.naviURL.role}"><i class="glyphicon glyphicon-knight"></i> 角色管理</a></li>
        <li class="${curChannelId=='userrole'? 'active':''}"><a href="${ this.naviURL.userrole}"><i class="glyphicon glyphicon-lock"></i> 用户角色分配</a></li>
        </ul>
        <hr/>
        <ul class="nav nav-sidebar">
            <li class="${curChannelId=='log'? 'active':''}"><a href="${ this.naviURL.log}"><i class="glyphicon glyphicon-book"></i> 日志列表</a></li>

        </ul>
        <hr/>
        <ul class="nav nav-sidebar">
            <li><a href="${ this.naviURL.logout}"><i class="glyphicon glyphicon-user"></i> 退出系统</a></li>

        </ul>`;
        return htmlStr
    }



}

let j79App=new PageManager();
$('#topNaviContainer').empty();
$('#topNaviContainer').append(j79App.getTopNaviHtml());
class PageManager{
    constructor(params) {

        this.naviURL={
            "home":"#",
            "user":"user.html",
            "privilege":"#",
            "role":"role.html",
            "log":"log.html",
            "login":"login.html",
            "logout":"login.html",
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
           
            <li><a href="${ this.naviURL.log}"  target="_top">日志</a></li>
            <li><a href="${ this.naviURL.logout}"  target="_top"><i class="glyphicon glyphicon-off"></i> 退出</a></li>`;

        return htmlStr;
    }//-/



}

let j79App=new PageManager();
$('#topNaviContainer').empty();
$('#topNaviContainer').append(j79App.getTopNaviHtml());
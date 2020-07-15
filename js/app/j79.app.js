class PageManager{
    constructor(params) {

        this.naviURL={
            "home":"home.html",
            "user":"user.html",
            "privilege":"/j79frame/pages/app/privilege.html",
            "role":"role.html",
            "log":"log.html",
            "login":"login.html",
            "logout":"logout.html",
        };
    }//-/

    getTopNaviHtml(){
        let htmlStr=`<li><a href="${ this.naviURL.home}" target="_top">首页</a></li>
            <li><a href="${ this.naviURL.user}"  target="_top">用户</a></li>
            <li><a href="${ this.naviURL.privilege}"  target="_top">权限</a></li>
            <li><a href="${ this.naviURL.role}"  target="_top">角色</a></li>
            <li><a href="${ this.naviURL.log}"  target="_top">日志</a></li>
            <li><a href="${ this.naviURL.logout}"  target="_top"><i class="glyphicon glyphicon-off"></i> 退出</a></li>`;

        return htmlStr;
    }//-/
}

let j79App=new PageManager();
$('#topNaviContainer').empty();
$('#topNaviContainer').append(j79App.getTopNaviHtml());